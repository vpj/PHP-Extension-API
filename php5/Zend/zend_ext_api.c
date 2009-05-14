#include "zend.h"
#include "zend_ext_api.h"

HashTable ext_api_registry;
/* TODO: Check if memory allocations fail */

struct _zend_ext_api_extension {
	char *ext_name;
	int version;
	size_t size;
	void *api;
};

typedef struct _zend_ext_api_extension zend_ext_api_extension;

void zend_ext_api_free_api(void *api)
{
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *)api;
	/* TODO: Free memory */
}

void zend_ext_api_init()
{
    zend_hash_init_ex(&ext_api_registry, 10, NULL, zend_ext_api_free_api, 1, 0);
}

zend_ext_api_extension * zend_ext_api_create(char *ext_name, int version, void *api, size_t size)
{
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *) pemalloc_rel(sizeof(zend_ext_api_extension), ext_api_registery->persistent);
 
    ext_api->version = version;
	ext_api->size = size;
	ext_api->ext_name = strdup(ext_name);
	ext_api->api = (void *)pemalloc_rel(size, ext_api_registry->persistent);
    memcpy(ext_api->api, api, size);

	return ext_api;
}

char * zend_ext_api_hash_name(char *ext_name, int version)
{
	char *hash_name;

	sprintf(hash_name, "%s-%d", ext_name, version);

	return hash_name;
}

ZEND_API int zend_ext_api_register(char *ext_name, int version, void *api, size_t size)
{
	char *hash_name = zend_ext_api_hash_name(ext_name, version);
	/* TODO: Clear hash_name memory */
	zend_ext_api_extension *ext_api = zend_ext_api_create(ext_name, version, api, size);
	/* Check errors when creating extension */

	int r = zend_hash_add(&ext_api_registry, hash_name, strlen(hash_name) + 1, api, size, NULL);
	/* TODO: Clear ext_api memory */

	return r;
}

ZEND_API int zend_ext_api_exists(char *ext_name, int version)
{
	char *hash_name = zend_ext_api_hash_name(ext_name, version);
	/* TODO: Clear hash_name memory */
	return zend_hash_exists(&ext_api_registry, hash_name, strlen(hash_name) + 1);
}

ZEND_API int zend_ext_api_get(char *ext_name, int version, void **api)
{
	char *hash_name = zend_ext_api_hash_name(ext_name, version);
	/* TODO: Clear hash_name memory */
	return zend_hash_find(&ext_api_registry, hash_name, strlen(hash_name) + 1, api);
}

