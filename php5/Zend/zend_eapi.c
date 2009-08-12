#include "zend.h"
#include "zend_eapi.h"
#include "zend_globals.h"
#include "zend_globals_macros.h"
#include <ctype.h>

#define _REG_PRST TRUE
#define _REG_VER_PRST TRUE
#define _LIST_CB_PRST TRUE

#define _REG EG(eapi_registry)
#define _REG_VER EG(eapi_reg_ver)
#define _CB_LIST EG(eapi_callback_list)
#define _NEW_EXT_NAME EG(eapi_new_ext_name)
#define _NEW_EXT_VER EG(eapi_new_ext_version)
#define _NEW_MOD_NO EG(eapi_new_module_number)

/* {{{ zend_eapi_cb
 * Structure for Callbacks */
struct _zend_eapi_cb {
	char *ext_name;
	uint version;
	int latest;
	int module_number;
	int type;
	int called;
	void (*callback_func)(EAPI_CALLBACK_FUNC_ARGS);
	void (*callback_func_empty)(EAPI_EMPTY_CALLBACK_FUNC_ARGS);
};

typedef struct _zend_eapi_cb zend_eapi_cb;
/* }}} */

/* {{{ zend_eapi_extension
 * Wrapper structure for APIs */
struct _zend_eapi_extension {
	char *ext_name;
	uint version;
	char *version_text;
	size_t size;
	void *api;
};

typedef struct _zend_eapi_extension zend_eapi_extension;
/* }}} */

/* {{{ zend_eapi_ver_llist
 * A simple linked list to store versions of API's */
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
/* }}} */

/* {{{ Function prototypes */
static int zend_eapi_ver_llist_destroy(zend_eapi_ver_llist *ll);
/* }}} */

/* {{{ static void zend_eapi_free_ver_llist(void *llist)
 * Free the versions linked list */
static void zend_eapi_free_ver_llist(void *llist)
{
	zend_eapi_ver_llist_destroy((zend_eapi_ver_llist *)llist);
}
/* }}} */

/* {{{ static void zend_eapi_free_api(void *api)
 * Free extension API structure */
static void zend_eapi_free_api(void *api)
{
	zend_eapi_extension *ext_api = (zend_eapi_extension *)api;
	
	/* Free memory */
	pefree(ext_api->ext_name, _REG_PRST);
	pefree(ext_api->version_text, _REG_PRST);

	pefree(ext_api->api, _REG_PRST);
	
	/* The hash entry will be freed by the hash table */
	/* pefree(ext_api, _REG.persistent); */
}
/* }}} */

/* {{{ static void zend_eapi_free_callback(void *callback)
 * Free callback structure */
static void zend_eapi_free_callback(void *callback)
{
	zend_eapi_cb *cb = (zend_eapi_cb *)callback;

	pefree(cb->ext_name, _LIST_CB_PRST);
}
/* }}} */

/* {{{ void zend_eapi_destroy()
 * Free hashtables */
void zend_eapi_destroy()
{
	/* Destroy the hash table */
	/* zend_hash_graceful_reverse_destroy(&module_registry); We don't need this at the moment*/
	zend_hash_destroy(&_REG);
	zend_hash_destroy(&_REG_VER);
	zend_llist_destroy(&_CB_LIST);
}
/* }}} */

/* {{{ void zend_eapi_init()
 * Initialize hashtables and lists */
void zend_eapi_init()
{
	if(zend_hash_init_ex(&_REG, 10, NULL, zend_eapi_free_api, /* persistent */_REG_PRST, 0) == FAILURE) {
		ZEND_PUTS("Unable to initialize the HashTable\n");
	}

	if(zend_hash_init_ex(&_REG_VER, 10, NULL, zend_eapi_free_ver_llist, /* persistent */_REG_VER_PRST, 0) == FAILURE) {
		ZEND_PUTS("Unable to initialize the versions HashTable\n");
	}
	
	zend_llist_init(&_CB_LIST, sizeof(zend_eapi_cb), zend_eapi_free_callback, _LIST_CB_PRST);

	_NEW_EXT_NAME = NULL;
	_NEW_MOD_NO = FALSE;
}
/* }}} */

/* {{{ static zend_eapi_extension * zend_eapi_create(char *ext_name, uint version, char *version_text, void *api, size_t size)
 * Wrap the extension api in zend_eapi_extension */
static zend_eapi_extension * zend_eapi_create(char *ext_name, uint version, char *version_text, void *api, size_t size)
{
	zend_eapi_extension *ext_api = (zend_eapi_extension *)pemalloc(sizeof(zend_eapi_extension), _REG_PRST);

	if(!ext_api) {
		return NULL;
	}

 	ext_api->version = version;
	ext_api->version_text = strdup(version_text);
	ext_api->size = size;
	ext_api->ext_name = strdup(ext_name);
	ext_api->api = (void *)pemalloc(size, _REG_PRST);
	
	if(!(ext_api->api))	{
		return NULL;
	}

	memcpy(ext_api->api, api, size);

	return ext_api;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_version_toi(char *version_text, uint *version_int)
 * Convert version from text (major.minor.[.build[.revision]] to numerical form */
ZEND_API int zend_eapi_version_toi(char *version_text, uint *version_int)
{
	uint t;
	int i;
	uint mul = 256 * 256 * 256;

	*version_int = 0;

	t = 0;
	for(i = 0; TRUE; i++) {
		if(isdigit(version_text[i])) {
			t *= 10;
			t += version_text[i] - '0';

			if(t >= 256) {
				return FAILURE;
			}
		}
		else if(version_text[i] == '.' || version_text[i] == '\0') {
			*version_int += t * mul;
			mul /= 256;
			t = 0;

			if(version_text[i] == '\0')	{
				return SUCCESS;
			}

			if(mul <= 0) {
				return FAILURE;
			}
		}
	}
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_version_toa(uint version, char ** version_text)
 * Covert version from numerical to text format */
ZEND_API int zend_eapi_version_toa(uint version, char ** version_text)
{
	int a[4] = {0, 0, 0, 0};
	uint mul = 256 * 256 * 256;
	int i;

	/* Maximum length 255.255.255.255 */
	*version_text = pemalloc(16, _REG_PRST);

	if(!(*version_text)) {
		return FAILURE;
	}

	for(i = 0; i < 4; i++) {
		a[i] = (int)(version / mul);
		version %= mul;
		mul /= 256;
	}

	sprintf(*version_text, "%d.%d.%d.%d", a[0], a[1], a[2], a[3]);

	return SUCCESS;
}
/* }}} */

/* {{{ static int zend_eapi_get_hashname(char *ext_name, uint version, char ** hash_name)
 * API's are stored in the hashtable with the extension name postfixed by the version. This function finds the key */ 
static int zend_eapi_get_hashname(char *ext_name, uint version, char ** hash_name)
{
	*hash_name = pemalloc(strlen(ext_name) + 10, _REG_PRST); 

	if(!(*hash_name)) {
		return FAILURE;
	}

	sprintf(*hash_name, "%s-%x", ext_name, version);

	return SUCCESS;
}
/* }}} */

/* {{{ int zend_eapi_callback()
 * This function is called from zend_startup_extensions function after all
 * extensions have gone through MINIT. This will call all the callbacks */
int zend_eapi_callback(TSRMLS_D)
{
	zend_llist_element *element;
	void *api;
	zend_eapi_cb *cb;
	char *ext_name;
	uint version;

	for(element = _CB_LIST.head; element; element = element->next) {
		cb = (zend_eapi_cb *) element->data;

		if(cb->ext_name) {
			/* If the callback has to be called with the API of the specified version */
			if(!cb->latest) {
				if(zend_eapi_get_int_ver(cb->ext_name, cb->version, &api) == SUCCESS) {	
					ext_name = strdup(cb->ext_name);
					
					cb->called = TRUE;
					cb->callback_func(cb->type, cb->module_number, api, ext_name, cb->version, FALSE TSRMLS_CC);
				}
			}
			/* If the callback has to be called with the latest API */
			else {
				/* Get the latest version */
				if(zend_eapi_get_latest_version(cb->ext_name, &version) == SUCCESS)	{	
					if(zend_eapi_get_int_ver(cb->ext_name, version, &api) == SUCCESS) {	
						ext_name = strdup(cb->ext_name);

						cb->called = TRUE;
						cb->callback_func(cb->type, cb->module_number, api, ext_name, version, FALSE TSRMLS_CC);
					}
				}
			}
		}
		/* If the empty callback has to be called */
		else {
			cb->called = TRUE;
			cb->callback_func_empty(cb->type, cb->module_number, NULL, 0, FALSE TSRMLS_CC);
		}
	}

	if(_NEW_EXT_NAME) {
		pefree(_NEW_EXT_NAME, TRUE);
		_NEW_EXT_NAME = NULL;
	}

	_NEW_MOD_NO = FALSE;

	return SUCCESS;
}
/* }}} */

/* {{{ int zend_eapi_new_extension_callback()
 * This funciton is called when a extension is loaded through dl() and initialized
 * */
int zend_eapi_new_extension_callback(TSRMLS_D)
{
	zend_llist_element *element;
	void *api;
	zend_eapi_cb *cb;
	char *ext_name;
	uint version;
	
	/* New extension not registered */
	if(!_NEW_EXT_NAME && !_NEW_MOD_NO) {
		return FAILURE;
	}

	/* Calling call backs of other extensions */

	for(element = _CB_LIST.head; element; element = element->next) {
		cb = (zend_eapi_cb *) element->data;

		if(cb->ext_name) {
			if(_NEW_EXT_NAME && strcmp(cb->ext_name, _NEW_EXT_NAME) == 0) {
				if(!cb->latest && _NEW_EXT_VER != cb->version) {
					continue;
				} else if(cb->latest) {
					if(zend_eapi_get_latest_version(cb->ext_name, &version) == SUCCESS) {
						if(version != _NEW_EXT_VER) {
							continue;
						}
					} else {
						continue;
					}
				}

				version = _NEW_EXT_VER;
			} else if(cb->module_number == _NEW_MOD_NO) {
				if(cb->latest) {
					if(zend_eapi_get_latest_version(cb->ext_name, &version) != SUCCESS)	{
						continue;
					}
				} else {
					version = cb->version;
				}
			} else {
				continue;
			}

			if(zend_eapi_get_int_ver(cb->ext_name, version, &api) == SUCCESS) {	
				ext_name = strdup(cb->ext_name);

				cb->callback_func(cb->type, cb->module_number, api, ext_name, version, cb->called TSRMLS_CC);
				cb->called = TRUE;
			}
		}
		/* If the empty callback has to be called */
		else {
			if(cb->module_number == _NEW_MOD_NO) {
				cb->callback_func_empty(cb->type, cb->module_number, NULL, 0, cb->called TSRMLS_CC);
				cb->called = TRUE;
			} else if(_NEW_EXT_NAME) {
				cb->callback_func_empty(cb->type, cb->module_number, _NEW_EXT_NAME, _NEW_EXT_VER, cb->called TSRMLS_CC);
				cb->called = TRUE;
			}
		}
	}

	if(_NEW_EXT_NAME) {
		pefree(_NEW_EXT_NAME, TRUE);
		_NEW_EXT_NAME = NULL;
	}

	_NEW_MOD_NO = FALSE;
	
	return SUCCESS;

}
/* }}} */

/* {{{ static int zend_eapi_ver_llist_create(zend_eapi_ver_llist **ll)
 * Create the llist to store the set of versions for each extension */
static int zend_eapi_ver_llist_create(zend_eapi_ver_llist **ll)
{
	*ll = pemalloc(sizeof(zend_eapi_ver_llist), _REG_VER_PRST);

	if(!(*ll)) {
		return FAILURE;
	}

	(*ll)->first = NULL;
	(*ll)->size = 0;

	return SUCCESS;
}
/* }}} */

/* {{{ static int zend_eapi_ver_llist_add(zend_eapi_ver_llist *ll, uint version)
 * Add an element to the list of versions */
static int zend_eapi_ver_llist_add(zend_eapi_ver_llist *ll, uint version)
{
	zend_eapi_ver_llist_node *n = pemalloc(sizeof(zend_eapi_ver_llist_node), _REG_VER_PRST);

	if(!n) {
		return FAILURE;
	}

	/* The greatest version is kept at the top of the llist */
	if(!ll->first || version >= ll->first->version)	{
		n->version = version;
	}
	else {
		n->version = ll->first->version;
		ll->first->version = version;
	}

	n->next = ll->first;
	ll->first = n;
	ll->size++;

	return SUCCESS;
}
/* }}} */

/* {{{ static int zend_eapi_ver_llist_destroy(zend_eapi_ver_llist *ll)
 * Destroy the llist of versions */
static int zend_eapi_ver_llist_destroy(zend_eapi_ver_llist *ll)
{
	zend_eapi_ver_llist_node *cur = ll->first;
	zend_eapi_ver_llist_node *next = NULL;
	
	while(cur) {
		next = cur->next;
		pefree(cur, _REG_VER_PRST);
		cur = next;
	}

	ll->first = NULL;

	return SUCCESS;
}
/* }}} */

/* {{{ static int zend_eapi_add_version(char *ext_name, uint version_int)
 * Add the version to the hashtable of available versions.
 * Each entry in the hashtable (by extension name) is a llist of available versions */
static int zend_eapi_add_version(char *ext_name, uint version_int)
{
	zend_eapi_ver_llist *ll;

	if(zend_hash_exists(&_REG_VER, ext_name, strlen(ext_name) + 1)) {
		/* Multiple versions */
		if(zend_hash_find(&_REG_VER, ext_name, strlen(ext_name) + 1, (void **)(&ll)) == FAILURE) {	
			return FAILURE;
		}
		
		if(zend_eapi_ver_llist_add(ll, version_int) == FAILURE)	{
			return FAILURE;
		}
	}
	else {
		if(zend_eapi_ver_llist_create(&ll) == FAILURE) {
			return FAILURE;
		}

		if(zend_eapi_ver_llist_add(ll, version_int) == FAILURE) {
			zend_eapi_ver_llist_destroy(ll);
			pefree(ll, _REG_VER_PRST);

			return FAILURE;
		}

		if(zend_hash_add(&_REG_VER, ext_name, strlen(ext_name) + 1, ll, sizeof(zend_eapi_ver_llist), NULL) == FAILURE) {
			zend_eapi_ver_llist_destroy(ll);
			pefree(ll, _REG_VER_PRST);

			return FAILURE;
		}

		pefree(ll, _REG_VER_PRST);
	}

	return SUCCESS;
}
/* }}} */

/* {{{ static int zend_eapi_ver_llist_find(zend_eapi_ver_llist *ll, uint version, uint mask, uint *results, int *size, int buf_length)
 * Finds the set of matching versions in the linked list.
 * A version is a match if (extension_version & mask) == (version & mask) */
static int zend_eapi_ver_llist_find(zend_eapi_ver_llist *ll, uint version, uint mask, uint *results, int *size, int buf_length)
{
	zend_eapi_ver_llist_node *n = ll->first;
	*size = 0;

	while(n) {
		if((n->version & mask) == (version & mask))	{
			*results++ = n->version;
			(*size)++;

			if(*size > buf_length) {
				break;
			}
		}

		n = n->next;
	}

	return SUCCESS;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_find_versions(char *ext_name, uint version, uint mask, uint *result, int *size, int buf_length)
 * Finds the set of matching versions.
 * A version is a match if (extension_version & mask) == (version & mask) */
ZEND_API int zend_eapi_find_versions(char *ext_name, uint version, uint mask, uint *result, int *size, int buf_length)
{
	zend_eapi_ver_llist *ll;

	if(zend_hash_find(&_REG_VER, ext_name, strlen(ext_name) + 1, (void **)&ll) == FAILURE) {
		return FAILURE;
	}

	return zend_eapi_ver_llist_find(ll, version, mask, result, size, buf_length);
}
/* }}} */

/* {{{ static int zend_eapi_add(char *ext_name, char *version_text, uint version_int, void *api, size_t size)
 * Add (register) an extension API (internal function) */
static int zend_eapi_add(char *ext_name, char *version_text, uint version_int, void *api, size_t size)
{
	int r;
	char *hash_name;
	zend_eapi_extension *ext_api;

	/* Extensions are added to the hash table by hash_name, which is ext_name + '-' + version */
	if(zend_eapi_get_hashname(ext_name, version_int, &hash_name) == FAILURE) {
		ZEND_PUTS("Error creating the hash name\n");
		
		return FAILURE;
	}

	/* Wrap the api structure */
	ext_api = zend_eapi_create(ext_name, version_int, version_text, api, size);

	if(!ext_api) {
		pefree(hash_name, _REG_PRST);
		ZEND_PUTS("Memory allocation error\n");
		
		return FAILURE;
	}

	/* Add the API to the hash table */
	r = zend_hash_add(&_REG, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_eapi_extension), NULL);

	if(r == SUCCESS) {
		/* Add the version to the list of versions */
		r = zend_eapi_add_version(ext_name, version_int);
	}

	if(_NEW_EXT_NAME) {
		pefree(_NEW_EXT_NAME, TRUE);
	}

	_NEW_EXT_NAME = pestrdup(ext_name, TRUE);
	_NEW_EXT_VER = version_int;

	pefree(ext_api, _REG_PRST);
	pefree(hash_name, _REG_PRST);

	return r;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_register(char *ext_name, char * version_text, void *api, size_t size)
 * Register an API */
ZEND_API int zend_eapi_register(char *ext_name, char * version_text, void *api, size_t size)
{
	uint version_int;

	/* Parse the version number */
	if(zend_eapi_version_toi(version_text, &version_int) == FAILURE) {
		ZEND_PUTS("Invalid version format\n");
		
		return FAILURE;
	}

	return zend_eapi_add(ext_name, version_text, version_int, api, size);
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_register_int_ver(char *ext_name, uint version_int, void *api, size_t size)
 * Register an API with the version specified as an uint */
ZEND_API int zend_eapi_register_int_ver(char *ext_name, uint version_int, void *api, size_t size)
{
	char *version_text;
	int r;

	if(zend_eapi_version_toa(version_int, &version_text) == FAILURE) {
		ZEND_PUTS("Cannot convert version number to a string\n");
		
		return FAILURE;
	}

	r = zend_eapi_add(ext_name, version_text, version_int, api, size);
	
	pefree(version_text, _REG_PRST);

	return r;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_exists_int_ver(char *ext_name, uint version)
 * Check if the extension API is available (version as a uint)
 * Returns TRUE if exists FALSE otherwise */
ZEND_API int zend_eapi_exists_int_ver(char *ext_name, uint version)
{
	char *hash_name;
	int r;

	if(zend_eapi_get_hashname(ext_name, version, &hash_name) == FAILURE) {
		ZEND_PUTS("Error creating the hash name\n");
		
		return FALSE;
	}
	
	r = zend_hash_exists(&_REG, hash_name, strlen(hash_name) + 1);
	
	pefree(hash_name, _REG_PRST);

	return r;
}
/* }}} */

/* {{{ static uint zend_eapi_ver_llist_max(zend_eapi_ver_llist *ll)
 * Finds the highest version in a llist of versions */
static uint zend_eapi_ver_llist_max(zend_eapi_ver_llist *ll)
{
	return ll->first->version;

	/*
	uint m = 0;
	zend_eapi_ver_llist_node *n = ll->first;

	while(n) {
		m = m < n->version ? n->version : m;
		n = n->next;
	}

	return m;
	*/
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_get_latest_version(char *ext_name, uint *version)
 * Gets the latest version of the extension API */
ZEND_API int zend_eapi_get_latest_version(char *ext_name, uint *version)
{
	zend_eapi_ver_llist *ll;

	if(zend_hash_find(&_REG_VER, ext_name, strlen(ext_name) + 1, (void **)&ll) == FAILURE) {
		return FAILURE;
	}

	*version = zend_eapi_ver_llist_max(ll);

	return SUCCESS;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_exists(char *ext_name, char *version)
 * Check if the extension API is available.
 * Returns TRUE if exists FALSE otherwise */
ZEND_API int zend_eapi_exists(char *ext_name, char *version)
{
	uint vi;

	if(zend_eapi_version_toi(version, &vi) == FAILURE) {
		ZEND_PUTS("Invalid version format\n");
		
		return FALSE;
	}
							
	return zend_eapi_exists_int_ver(ext_name, vi);
}
/* }}} */

/* {{{ static int zend_eapi_set_callback_int_ver(int type, int module_number, char *ext_name, uint version, int latest, void (*callback_func)(EAPI_CALLBACK_FUNC_ARGS))
 * Registers an callback function.
 * The callback function would be called if the required extension API is available,
 * after all the extensions have been initialized (MINIT).
 * If latest is not 0, then the version is ignored and the API of the latest version is passed to the callback */
static int zend_eapi_set_callback_int_ver(int type, int module_number, char *ext_name, uint version, int latest, void (*callback_func)(EAPI_CALLBACK_FUNC_ARGS))
{
	zend_eapi_cb cb;
	cb.type = type;
	cb.module_number = module_number;
	cb.ext_name = strdup(ext_name);
	cb.version = version;
	cb.latest = latest;
	cb.called = FALSE;
	cb.callback_func = callback_func;
	cb.callback_func_empty = NULL;

	zend_llist_add_element(&_CB_LIST, &cb);

	_NEW_MOD_NO = module_number;

	return SUCCESS;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_set_empty_callback(int type, int module_number, void (*callback)(EAPI_EMPTY_CALLBACK_FUNC_ARGS))
 * Sets an empty callback which accepts 0 parameters */
ZEND_API int zend_eapi_set_empty_callback(int type, int module_number, void (*callback)(EAPI_EMPTY_CALLBACK_FUNC_ARGS))
{
	zend_eapi_cb cb;
	cb.type = type;
	cb.module_number = module_number;
	cb.ext_name = NULL;
	cb.version = 0;
	cb.latest = 0;
	cb.called = FALSE;
	cb.callback_func = NULL;
	cb.callback_func_empty = callback;

	zend_llist_add_element(&_CB_LIST, &cb);

	_NEW_MOD_NO = module_number;

	return SUCCESS;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_set_callback(int type, int module_number, char *ext_name, char *version, void (*callback_func)(EAPI_CALLBACK_FUNC_ARGS))
 * Registers an callback function.
 * The callback function would be called if the required extension API is available,
 * after all the extensions have been initialized (MINIT) 
 *
 * If version is NULL the latest API is used */
ZEND_API int zend_eapi_set_callback(int type, int module_number, char *ext_name, char *version, void (*callback_func)(EAPI_CALLBACK_FUNC_ARGS))
{
	uint vi;

	if(!version) {
		return zend_eapi_set_callback_int_ver(type, module_number, ext_name, 0, TRUE, callback_func);
	}

	if(zend_eapi_version_toi(version, &vi) == FAILURE) {
		return FAILURE;
	}

	return zend_eapi_set_callback_int_ver(type, module_number, ext_name, vi, FALSE, callback_func);
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_get_int_ver(char *ext_name, uint version, void **api)
 * Retrives the API if available; otherwise returns FAILURE */
ZEND_API int zend_eapi_get_int_ver(char *ext_name, uint version, void **api)
{
	char *hash_name;
	zend_eapi_extension *ext_api;
	
	if(zend_eapi_get_hashname(ext_name, version, &hash_name) == FAILURE) {
		ZEND_PUTS("Error creating the hash name\n");
		
		return FAILURE;
	}
	
	if(zend_hash_find(&_REG, hash_name, strlen(hash_name) + 1, (void **)(&ext_api)) == FAILURE) {
		pefree(hash_name, _REG_PRST);
	
		return FAILURE;
	}

	*api = ext_api->api;

	pefree(hash_name, _REG_PRST);

	return SUCCESS;
}
/* }}} */

/* {{{ ZEND_API int zend_eapi_get(char *ext_name, char *version, void **api)
 * Retrives the API if available; otherwise returns FAILURE */
ZEND_API int zend_eapi_get(char *ext_name, char *version, void **api)
{
	uint vi;

	if(zend_eapi_version_toi(version, &vi) == FAILURE) {
		ZEND_PUTS("Invalid version format\n");
		
		return FAILURE;
	}
							
	return zend_eapi_get_int_ver(ext_name, vi, api);
}
/* }}} */

