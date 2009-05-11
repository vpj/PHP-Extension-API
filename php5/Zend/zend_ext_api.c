#include "zend.h"
#include "zend_ext_api.h"

ZEND_API HashTable ext_api_registry;

void zend_ext_api_free_api(void *api)
{
}

void zend_ext_api_init()
{
    zend_hash_init_ex(&ext_api_registry, 10, NULL, zend_ext_api_free_api, 1, 0);
}

ZEND_API int zend_ext_api_register(char *ext_name, int version, void *api, size_t size)
{
	return zend_hash_add(&ext_api_registry, ext_name, strlen(ext_name) + 1, api, size, NULL);
}

ZEND_API int zend_ext_api_exists(char *ext_name, int version)
{
	return zend_hash_exists(&ext_api_registry, ext_name, strlen(ext_name) + 1);
}

ZEND_API int zend_ext_api_get(char *ext_name, int version, void **api)
{
	return zend_hash_find(&ext_api_registry, ext_name, strlen(ext_name) + 1, api);
}

