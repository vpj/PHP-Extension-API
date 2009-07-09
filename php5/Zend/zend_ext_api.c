#include "zend.h"
#include "zend_ext_api.h"
#include <ctype.h>

#define _REG_PRST eapi_registry.persistent
#define _REG_VER_PRST eapi_reg_ver.persistent
#define _LIST_CB eapi_callback_list.persistent

/* Stores all the APIs */
HashTable eapi_registry;
/* Stores the versions */
HashTable eapi_reg_ver;
zend_llist eapi_callback_list;

/* Structure for Callbacks */
struct _zend_eapi_cb {
	char *ext_name;
	uint version;
	int latest;
	void (*callback_func)(void *api, char *ext_name, uint version);
	void (*callback_func_empty)();
};

typedef struct _zend_eapi_cb zend_eapi_cb;

/* Wrapper structure for APIs */
struct _zend_eapi_extension {
	char *ext_name;
	uint version;
	char *version_text;
	size_t size;
	void *api;
};

typedef struct _zend_eapi_extension zend_eapi_extension;

/* A simple linked list to store versions of API's */
typedef struct _zend_eapi_ver_llist_node zend_eapi_ver_llist_node;

struct _zend_eapi_ver_llist_node {
	int version;
	zend_eapi_ver_llist_node *next;
};

typedef struct _zend_eapi_ver_llist zend_eapi_ver_llist;

struct _zend_eapi_ver_llist {
	int size;
	zend_eapi_ver_llist_node *first;
};

/* Function prototypes */
int zend_eapi_ver_llist_destroy(zend_eapi_ver_llist *ll);

/* Free the versions linked list */
void zend_eapi_free_ver_llist(void *llist)
{
	zend_eapi_ver_llist_destroy((zend_eapi_ver_llist *)llist);
}

/* Free extension API structure */
void zend_eapi_free_api(void *api)
{
	zend_eapi_extension *ext_api = (zend_eapi_extension *)api;
	
	/* Free memory */
	pefree(ext_api->ext_name, _REG_PRST);
	pefree(ext_api->version_text, _REG_PRST);

	pefree(ext_api->api, _REG_PRST);
	
	/* The hash entry will be freed by the hash table */
	/* pefree(ext_api, eapi_registry.persistent); */
}

/* Free callback structure */
void zend_eapi_free_callback(void *callback)
{
	zend_eapi_cb *cb = (zend_eapi_cb *)callback;

	pefree(cb->ext_name, _LIST_CB);
}

/* Free hashtables */
void zend_eapi_destroy()
{
	/* Destroy the hash table */
	/* zend_hash_graceful_reverse_destroy(&module_registry); We don't need this at the moment*/
	zend_hash_destroy(&eapi_registry);
	zend_hash_destroy(&eapi_reg_ver);
	zend_llist_destroy(&eapi_callback_list);
}

/* Initialize hashtables and lists */
void zend_eapi_init()
{
	if(zend_hash_init_ex(&eapi_registry, 10, NULL, zend_eapi_free_api, /* persistent */1, 0) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Unable to initialize the HashTable\n");
#endif
	}

	if(zend_hash_init_ex(&eapi_reg_ver, 10, NULL, zend_eapi_free_ver_llist, /* persistent */1, 0) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Unable to initialize the versions HashTable\n");
#endif
	}
	
	zend_llist_init(&eapi_callback_list, sizeof(zend_eapi_cb), zend_eapi_free_callback, 1);
}

/* Wrap the API */
zend_eapi_extension * zend_eapi_create(char *ext_name, uint version, char *version_text, void *api, size_t size)
{
	zend_eapi_extension *ext_api = (zend_eapi_extension *)pemalloc(sizeof(zend_eapi_extension), _REG_PRST);

	if(!ext_api)
	{
		return NULL;
	}

 	ext_api->version = version;
	ext_api->version_text = strdup(version_text);
	ext_api->size = size;
	ext_api->ext_name = strdup(ext_name);
	ext_api->api = (void *)pemalloc(size, _REG_PRST);
	
	if(!(ext_api->api))
	{
		return NULL;
	}

	memcpy(ext_api->api, api, size);

	return ext_api;
}

/* Convert version from text to numerical form */
ZEND_API int zend_eapi_version_toi(char *version_text, uint *version_int)
{
	uint t;
	int i;
	uint mul = 256 * 256 * 256;

	*version_int = 0;

	t = 0;
	for(i = 0; 1; i++)
	{
		if(isdigit(version_text[i]))
		{
			t *= 10;
			t += version_text[i] - '0';

			if(t >= 256)
			{
				return FAILURE;
			}
		}
		else if(version_text[i] == '.' || version_text[i] == '\0')
		{
			*version_int += t * mul;
			mul /= 256;
			t = 0;

			if(version_text[i] == '\0')
			{
				return SUCCESS;
			}

			if(mul <= 0)
			{
				return FAILURE;
			}
		}

	}
}

/* Covert version from numerical to text format */
ZEND_API int zend_eapi_version_toa(uint version, char ** version_text)
{
	int a[4] = {0, 0, 0, 0};
	uint mul = 256 * 256 * 256;
	int i;

	/* Maximum length 255.255.255.255 */
	*version_text = pemalloc(16, _REG_PRST);

	if(!(*version_text))
	{
		return FAILURE;
	}

	for(i = 0; i < 4; i++)
	{
		a[i] = (int)(version / mul);
		version %= mul;
		mul /= 256;
	}

	sprintf(*version_text, "%d.%d.%d.%d", a[0], a[1], a[2], a[3]);

	return SUCCESS;
}

/* API's are stored in the hashtable with the extension name
 * postfixed by the version. This function finds the key */ 
int zend_eapi_get_hashname(char *ext_name, uint version, char ** hash_name)
{
	*hash_name = pemalloc(strlen(ext_name) + 10, _REG_PRST); 

	if(!(*hash_name))
	{
		return FAILURE;
	}

	sprintf(*hash_name, "%s-%x", ext_name, version);

	return SUCCESS;
}

/* This function is called from zend_startup_extensions function after all
 * extensions have gone through MINIT.
 * This will call all the callbacks */
int zend_eapi_callback()
{
	zend_llist_element *element;
	void *api;
	zend_eapi_cb *cb;
	char *ext_name;
	uint version;

	for(element = eapi_callback_list.head; element; element = element->next)
	{
		cb = (zend_eapi_cb *) element->data;

		if(cb->ext_name)
		{
			/* If the callback has to be called with the API of the specified version */
			if(!cb->latest)
			{
				if(zend_eapi_get_int_ver(cb->ext_name, cb->version, &api) == SUCCESS)
				{	
					ext_name = strdup(cb->ext_name);

					cb->callback_func(api, ext_name, cb->version);
				}
			}
			/* If the callback has to be called with the latest API */
			else
			{
				/* Get the latest version */
				if(zend_eapi_get_latest_version(cb->ext_name, &version) == SUCCESS)
				{	
					if(zend_eapi_get_int_ver(cb->ext_name, version, &api) == SUCCESS)
					{	
						ext_name = strdup(cb->ext_name);

						cb->callback_func(api, ext_name, version);
					}
				}
			}
		}
		/* If the empty callback has to be called */
		else
		{
			cb->callback_func_empty();
		}
	}
	
	return SUCCESS;
}

/* Create the llist to store the set of versions for each extension */
int zend_eapi_ver_llist_create(zend_eapi_ver_llist **ll)
{
	*ll = pemalloc(sizeof(zend_eapi_ver_llist), _REG_VER_PRST);

	if(!(*ll))
	{
		return FAILURE;
	}

	(*ll)->first = NULL;
	(*ll)->size = 0;

	return SUCCESS;
}

/* Add an element to the list of versions */
int zend_eapi_ver_llist_add(zend_eapi_ver_llist *ll, uint version)
{
	zend_eapi_ver_llist_node *n = pemalloc(sizeof(zend_eapi_ver_llist_node), _REG_VER_PRST);

	if(!n)
	{
		return FAILURE;
	}

	n->version = version;
	n->next = ll->first;
	ll->first = n;
	ll->size++;

	return SUCCESS;
}

/* Destroy the llist of versions */
int zend_eapi_ver_llist_destroy(zend_eapi_ver_llist *ll)
{
	zend_eapi_ver_llist_node *cur = ll->first;
	zend_eapi_ver_llist_node *next = NULL;
	
	while(cur)
	{
		next = cur->next;

		pefree(cur, _REG_VER_PRST);

		cur = next;
	}

	ll->first = NULL;

	return SUCCESS;
}

/* Add the version to the hashtable of available versions.
 * Each entry in the hashtable (by extension name) is a llist
 * of available versions */
int zend_eapi_add_version(char *ext_name, uint version_int)
{
	zend_eapi_ver_llist *ll;

	/* TODO: Need to check if an entry already exists
	 * Functions to test if a particular version is present in a link list
	 * Functions to find the greatest version available
	 * Find API's in a range of versions
	 */

	if(zend_hash_exists(&eapi_reg_ver, ext_name, strlen(ext_name) + 1))
	{
		/* Multiple versions */
		if(zend_hash_find(&eapi_reg_ver, ext_name, strlen(ext_name) + 1, (void **)(&ll)) == FAILURE)
		{	
			return FAILURE;
		}
		
		if(zend_eapi_ver_llist_add(ll, version_int) == FAILURE)
		{
			return FAILURE;
		}
	}
	else
	{
		if(zend_eapi_ver_llist_create(&ll) == FAILURE)
		{
			return FAILURE;
		}

		if(zend_eapi_ver_llist_add(ll, version_int) == FAILURE)
		{
			zend_eapi_ver_llist_destroy(ll);
			pefree(ll, _REG_VER_PRST);

			return FAILURE;
		}

		if(zend_hash_add(&eapi_reg_ver, ext_name, strlen(ext_name) + 1, ll, sizeof(zend_eapi_ver_llist), NULL) == FAILURE)
		{
			zend_eapi_ver_llist_destroy(ll);
			pefree(ll, _REG_VER_PRST);

			return FAILURE;
		}
	}

	return SUCCESS;
}

/* Add (register) an extension API */
int zend_eapi_add(char *ext_name, char *version_text, uint version_int, void *api, size_t size)
{
	int r;
	char *hash_name;
	zend_eapi_extension *ext_api;

	/* Extensions are added to the hash table by hash_name, which is ext_name + '-' + version */
	if(zend_eapi_get_hashname(ext_name, version_int, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}

	/* Wrap the api structure */
	ext_api = zend_eapi_create(ext_name, version_int, version_text, api, size);
	if(!ext_api)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Memory allocation error\n");
#endif
		return FAILURE;
	}

	/* Add the API to the hash table */
	r = zend_hash_add(&eapi_registry, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_eapi_extension), NULL);

	if(r == SUCCESS)
	{
		/* Add the version to the list of versions */
		r = zend_eapi_add_version(ext_name, version_int);
	}

	/* Clear ext_api memory */
	pefree(ext_api, _REG_PRST);
	/* Clear hash_name memory */
	pefree(hash_name, _REG_PRST);

	return r;
}

/* Register an API */
ZEND_API int zend_eapi_register(char *ext_name, char * version_text, void *api, size_t size)
{
	uint version_int;

	/* Parse the version number */
	if(zend_eapi_version_toi(version_text, &version_int) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return FAILURE;
	}

	return zend_eapi_add(ext_name, version_text, version_int, api, size);
}

/* Register an API with the version specified as an uint */
ZEND_API int zend_eapi_register_int_ver(char *ext_name, uint version_int, void *api, size_t size)
{
	char *version_text;
	int r;

	if(zend_eapi_version_toa(version_int, &version_text) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Cannot convert version number to a string\n");
#endif
		return FAILURE;
	}

	r = zend_eapi_add(ext_name, version_text, version_int, api, size);
	
	/* Clear version_text memory */
	pefree(version_text, _REG_PRST);

	return r;
}

/* Check if the extension API is available.
 * Returns 1 if exists 0 otherwise */
ZEND_API int zend_eapi_exists_int_ver(char *ext_name, uint version)
{
	char *hash_name;
	int r;

	if(zend_eapi_get_hashname(ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return 0; /* FAILURE */
	}
	
	r = zend_hash_exists(&eapi_registry, hash_name, strlen(hash_name) + 1);
	
	/* Clear hash_name memory */
	pefree(hash_name, _REG_PRST);

	return r;
}

/* Finds the highest version in a llist of versions
 * TODO: Keep the highest version at the top so that
 * the time complexity is O(1) */
uint zend_eapi_ver_llist_max(zend_eapi_ver_llist *ll)
{
	uint m = 0;
	zend_eapi_ver_llist_node *n = ll->first;

	while(n)
	{
		m = m < n->version ? n->version : m;

		n = n->next;
	}

	return m;
}

/* Gets the latest version of the extension API */
ZEND_API int zend_eapi_get_latest_version(char *ext_name, uint *version)
{
	zend_eapi_ver_llist *ll;

	if(zend_hash_find(&eapi_reg_ver, ext_name, strlen(ext_name) + 1, (void **)&ll) == FAILURE)
	{
		return FAILURE;
	}

	*version = zend_eapi_ver_llist_max(ll);

	return SUCCESS;
}

/* Check if the extension API is available.
 * Returns 1 if exists 0 otherwise */
ZEND_API int zend_eapi_exists(char *ext_name, char *version)
{
	uint vi;

	if(zend_eapi_version_toi(version, &vi) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return 0; /*FAILURE*/
	}
							
	return zend_eapi_exists_int_ver(ext_name, vi);
}

/* Registers an callback function. The callback function would
 * be called if the required extension API is available, after all the extensions
 * have been initialized (MINIT).
 * If latest is not 0 then the version is ignored and the API of the latest version is used */
int zend_eapi_set_callback_int_ver(char *ext_name, uint version, int latest, void (*callback_func)(void *api, char *ext_name, uint version))
{
	zend_eapi_cb *cb = (zend_eapi_cb *)pemalloc(sizeof(zend_eapi_cb), _REG_PRST);
	cb->ext_name = strdup(ext_name);
	cb->version = version;
	cb->latest = latest;
	cb->callback_func = callback_func;
	cb->callback_func_empty = NULL;

	zend_llist_add_element(&eapi_callback_list, cb);
	
	/*pefree(cb, _REG_PRST);*/

	return SUCCESS;
}

/* Sets an empty callback which accepts 0 parameters */
ZEND_API int zend_eapi_set_empty_callback(void (*callback)())
{
	zend_eapi_cb cb;
	cb.ext_name = NULL;
	cb.version = 0;
	cb.latest = 0;
	cb.callback_func = NULL;
	cb.callback_func_empty = callback;

	zend_llist_add_element(&eapi_callback_list, &cb);

	return SUCCESS;
}

/* Registers an callback function. The callback function would
 * be called if the required extension API is available, after all the extensions
 * have been initialized (MINIT) 
 *
 * If version is NULL the latest API is used */
ZEND_API int zend_eapi_set_callback(char *ext_name, char *version, void (*callback_func)(void *api, char *ext_name, uint version))
{
	uint vi;

	if(!version)
	{
		return zend_eapi_set_callback_int_ver(ext_name, 0, 1, callback_func);
	}

	if(zend_eapi_version_toi(version, &vi) == FAILURE)
	{
		return FAILURE;
	}

	return zend_eapi_set_callback_int_ver(ext_name, vi, 0, callback_func);
}

/* Retrives the API if available; otherwise returns FAILURE */
ZEND_API int zend_eapi_get_int_ver(char *ext_name, uint version, void **api)
{
	char *hash_name;
	zend_eapi_extension *ext_api;
	
	if(zend_eapi_get_hashname(ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}
	
	if(zend_hash_find(&eapi_registry, hash_name, strlen(hash_name) + 1, (void **)(&ext_api)) == FAILURE)
	{
		return FAILURE;
	}

	/* WARNING: Should the api be passed back or a copy? */
	*api = ext_api->api;

	/* Clear hash_name memory */
	pefree(hash_name, _REG_PRST);

	return SUCCESS;
}

/* Retrives the API if available; otherwise returns FAILURE */
ZEND_API int zend_eapi_get(char *ext_name, char *version, void **api)
{
	uint vi;

	if(zend_eapi_version_toi(version, &vi) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return FAILURE;
	}
							
	return zend_eapi_get_int_ver(ext_name, vi, api);
}

