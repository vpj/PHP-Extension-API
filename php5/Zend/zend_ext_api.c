#include "zend.h"
#include "zend_ext_api.h"
#include <ctype.h>

#define _REG_PRST ext_api_registry.persistent
#define _REG_VER_PRST ext_api_reg_ver.persistent
#define _LIST_CB ext_api_callback_list.persistent

/* Stores all the APIs */
HashTable ext_api_registry;
/* Stores the versions */
HashTable ext_api_reg_ver;
zend_llist ext_api_callback_list;

/* Structure for Callbacks */
struct _zend_ext_api_cb {
	char *ext_name;
	uint version;
	void (*callback_func)(void *api, char *ext_name, uint version);
};

typedef struct _zend_ext_api_cb zend_ext_api_cb;

/* Wrapper structure for APIs */
struct _zend_ext_api_extension {
	char *ext_name;
	uint version;
	char *version_text;
	size_t size;
	void *api;
};

typedef struct _zend_ext_api_extension zend_ext_api_extension;

/* A simple linked list to store versions of API's */
typedef struct _zend_ext_api_ver_llist_node zend_ext_api_ver_llist_node;

struct _zend_ext_api_ver_llist_node {
	int version;
	zend_ext_api_ver_llist_node *next;
};

typedef struct _zend_ext_api_ver_llist zend_ext_api_ver_llist;

struct _zend_ext_api_ver_llist {
	int size;
	zend_ext_api_ver_llist_node *first;
};

/* Function prototypes */
int zend_ext_api_ver_llist_destroy(zend_ext_api_ver_llist *ll);

/* Free the versions linked list */
void zend_ext_api_free_ver_llist(void *llist)
{
	zend_ext_api_ver_llist_destroy((zend_ext_api_ver_llist *)llist);
}

/* Free extension API structure */
void zend_ext_api_free_api(void *api)
{
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *)api;
	
	/* Free memory */
	pefree(ext_api->ext_name, _REG_PRST);
	pefree(ext_api->version_text, _REG_PRST);

	pefree(ext_api->api, _REG_PRST);
	
	/* The hash entry will be freed by the hash table */
	/* pefree(ext_api, ext_api_registry.persistent); */
}

/* Free callback structure */
void zend_ext_api_free_callback(void *callback)
{
	zend_ext_api_cb *cb = (zend_ext_api_cb *)callback;

	pefree(cb->ext_name, _LIST_CB);
}

/* Free hashtables */
void zend_ext_api_destroy()
{
	/* Destroy the hash table */
	/* zend_hash_graceful_reverse_destroy(&module_registry); We don't need this at the moment*/
	zend_hash_destroy(&ext_api_registry);
	zend_hash_destroy(&ext_api_reg_ver);
	zend_llist_destroy(&ext_api_callback_list);
}

/* Initialize hashtables and lists */
void zend_ext_api_init()
{
	if(zend_hash_init_ex(&ext_api_registry, 10, NULL, zend_ext_api_free_api, /* persistent */1, 0) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Unable to initialize the HashTable\n");
#endif
	}

	if(zend_hash_init_ex(&ext_api_reg_ver, 10, NULL, zend_ext_api_free_ver_llist, /* persistent */1, 0) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Unable to initialize the versions HashTable\n");
#endif
	}
	
	zend_llist_init(&ext_api_callback_list, sizeof(zend_ext_api_cb), zend_ext_api_free_callback, 1);
}

/* Wrap the API */
zend_ext_api_extension * zend_ext_api_create(char *ext_name, uint version, char *version_text, void *api, size_t size)
{
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *)pemalloc(sizeof(zend_ext_api_extension), _REG_PRST);

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
ZEND_API int zend_ext_api_version_toi(char *version_text, uint *version_int)
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
ZEND_API int zend_ext_api_version_toa(uint version, char ** version_text)
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
int zend_ext_api_get_hashname(char *ext_name, uint version, char ** hash_name)
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
int zend_ext_api_callback()
{
	zend_llist_element *element;
	void *api;
	zend_ext_api_cb *cb;
	char *ext_name;

	php_printf("Callbacks...");

	for(element = ext_api_callback_list.head; element; element = element->next)
	{
		cb = (zend_ext_api_cb *) element->data;

		if(zend_ext_api_get_int_ver(cb->ext_name, cb->version, &api) == SUCCESS)
		{
			ext_name = strdup(cb->ext_name);

			cb->callback_func(api, ext_name, cb->version);
		}
	}
	
	return SUCCESS;
}

/* Create the llist to store the set of versions for each extension */
int zend_ext_api_ver_llist_create(zend_ext_api_ver_llist **ll)
{
	*ll = pemalloc(sizeof(zend_ext_api_ver_llist), _REG_VER_PRST);

	if(!(*ll))
	{
		return FAILURE;
	}

	(*ll)->first = NULL;
	(*ll)->size = 0;

	return SUCCESS;
}

/* Add an element to the list of versions */
int zend_ext_api_ver_llist_add(zend_ext_api_ver_llist *ll, uint version)
{
	zend_ext_api_ver_llist_node *n = pemalloc(sizeof(zend_ext_api_ver_llist_node), _REG_VER_PRST);

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
int zend_ext_api_ver_llist_destroy(zend_ext_api_ver_llist *ll)
{
	zend_ext_api_ver_llist_node *cur = ll->first;
	zend_ext_api_ver_llist_node *next = NULL;
	
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
int zend_ext_api_add_version(char *ext_name, uint version_int)
{
	zend_ext_api_ver_llist *ll;

	/* TODO: Need to check if an entry already exists
	 * Functions to test if a particular version is present in a link list
	 * Functions to find the greatest version available
	 * Find API's in a range of versions
	 */

	if(zend_hash_exists(&ext_api_reg_ver, ext_name, strlen(ext_name) + 1))
	{
		/* Multiple versions */
		if(zend_hash_find(&ext_api_reg_ver, ext_name, strlen(ext_name) + 1, (void **)(&ll)) == FAILURE)
		{	
			return FAILURE;
		}
		
		if(zend_ext_api_ver_llist_add(ll, version_int) == FAILURE)
		{
			return FAILURE;
		}
	}
	else
	{
		if(zend_ext_api_ver_llist_create(&ll) == FAILURE)
		{
			return FAILURE;
		}

		if(zend_ext_api_ver_llist_add(ll, version_int) == FAILURE)
		{
			zend_ext_api_ver_llist_destroy(ll);
			pefree(ll, _REG_VER_PRST);

			return FAILURE;
		}

		if(zend_hash_add(&ext_api_reg_ver, ext_name, strlen(ext_name) + 1, ll, sizeof(zend_ext_api_ver_llist), NULL) == FAILURE)
		{
			zend_ext_api_ver_llist_destroy(ll);
			pefree(ll, _REG_VER_PRST);

			return FAILURE;
		}
	}

	return SUCCESS;
}

/* Add (register) an extension API */
int zend_ext_api_add(char *ext_name, char *version_text, uint version_int, void *api, size_t size)
{
	int r;
	char *hash_name;
	zend_ext_api_extension *ext_api;

	/* Extensions are added to the hash table by hash_name, which is ext_name + '-' + version */
	if(zend_ext_api_get_hashname(ext_name, version_int, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}

	/* Wrap the api structure */
	ext_api = zend_ext_api_create(ext_name, version_int, version_text, api, size);
	if(!ext_api)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Memory allocation error\n");
#endif
		return FAILURE;
	}

	r = zend_hash_add(&ext_api_registry, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_ext_api_extension), NULL);

	if(r == SUCCESS)
	{
		r = zend_ext_api_add_version(ext_name, version_int);
	}

	/* Clear ext_api memory */
	pefree(ext_api, _REG_PRST);
	/* Clear hash_name memory */
	pefree(hash_name, _REG_PRST);

	return r;
}

/* Register an API */
ZEND_API int zend_ext_api_register(char *ext_name, char * version_text, void *api, size_t size)
{
	uint version_int;

	/* Parse the version number */
	if(zend_ext_api_version_toi(version_text, &version_int) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return FAILURE;
	}

	return zend_ext_api_add(ext_name, version_text, version_int, api, size);
}

/* Register an API */
int zend_ext_api_register_int_ver(char *ext_name, uint version_int, void *api, size_t size)
{
	char *version_text;
	int r;

	if(zend_ext_api_version_toa(version_int, &version_text) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Cannot convert version number to a string\n");
#endif
		return FAILURE;
	}

	r = zend_ext_api_add(ext_name, version_text, version_int, api, size);
	
	/* Clear version_text memory */
	pefree(version_text, _REG_PRST);

	return r;
}

/* Check if the extension API is available.
 * Returns 1 if exists 0 otherwise */
int zend_ext_api_exists_int_ver(char *ext_name, uint version)
{
	char *hash_name;
	int r;

	if(zend_ext_api_get_hashname(ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return 0; /* FAILURE */
	}
	
	r = zend_hash_exists(&ext_api_registry, hash_name, strlen(hash_name) + 1);
	
	/* Clear hash_name memory */
	pefree(hash_name, _REG_PRST);

	return r;
}

/* Finds the highest version in a llist of versions */
uint zend_ext_api_ver_llist_max(zend_ext_api_ver_llist *ll)
{
	uint m = 0;
	zend_ext_api_ver_llist_node *n = ll->first;

	while(n)
	{
		m = m < n->version ? n->version : m;

		n = n->next;
	}

	return m;
}

/* Gets the latest version of the extension API */
ZEND_API int zend_ext_api_get_latest_version(char *ext_name, uint *version)
{
	zend_ext_api_ver_llist *ll;

	if(zend_hash_find(&ext_api_reg_ver, ext_name, strlen(ext_name) + 1, (void **)&ll) == FAILURE)
	{
		return FAILURE;
	}

	*version = zend_ext_api_ver_llist_max(ll);

	return SUCCESS;
}

/* Check if the extension API is available.
 * Returns 1 if exists 0 otherwise */
ZEND_API int zend_ext_api_exists(char *ext_name, char *version)
{
	uint vi;

	if(zend_ext_api_version_toi(version, &vi) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return 0; /*FAILURE*/
	}
							
	return zend_ext_api_exists_int_ver(ext_name, vi);
}

/* Registers an callback function. The callback function would
 * be called if the required extension API is available, after all the extensions
 * have been initialized (MINIT) */
ZEND_API int zend_ext_api_set_callback_int_ver(char *ext_name, uint version, void (callback_func)(void *api, char *ext_name, uint version))
{
	zend_ext_api_cb cb;
	cb.ext_name = strdup(ext_name);
	cb.version = version;
	cb.callback_func = callback_func;

	zend_llist_add_element(&ext_api_callback_list, &cb);

	return SUCCESS;
}

/* Registers an callback function. The callback function would
 * be called if the required extension API is available, after all the extensions
 * have been initialized (MINIT) */
ZEND_API int zend_ext_api_set_callback(char *ext_name, char *version, void (callback_func)(void *api, char *ext_name, uint version))
{
	uint vi;

	if(zend_ext_api_version_toi(version, &vi) == FAILURE)
	{
		return FAILURE;
	}

	return zend_ext_api_set_callback_int_ver(ext_name, vi, callback_func);
}

/* Retrives the API if available */
int zend_ext_api_get_int_ver(char *ext_name, uint version, void **api)
{
	char *hash_name;
	zend_ext_api_extension *ext_api;
	
	if(zend_ext_api_get_hashname(ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}
	
	if(zend_hash_find(&ext_api_registry, hash_name, strlen(hash_name) + 1, (void **)(&ext_api)) == FAILURE)
	{
		return FAILURE;
	}

	/* WARNING: Should the api be passed back or a copy? */
	*api = ext_api->api;

	/* Clear hash_name memory */
	pefree(hash_name, _REG_PRST);

	return SUCCESS;
}

/* Retrives the API if available */
ZEND_API int zend_ext_api_get(char *ext_name, char *version, void **api)
{
	uint vi;

	if(zend_ext_api_version_toi(version, &vi) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return FAILURE;
	}
							
	return zend_ext_api_get_int_ver(ext_name, vi, api);
}

