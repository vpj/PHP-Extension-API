#include "zend.h"
#include "zend_ext_api.h"

ZEND_API HashTable ext_api_registry;
void ZEND_EXT_API_DTOR(void *pElement);

void zend_ext_api_init()
{
    zend_hash_init_ex(&ext_api_registry, 10, NULL, ZEND_EXT_API_DTOR, 1, 0);
}

void ZEND_EXT_API_DTOR(void *pElement)
{
}

ZEND_API int zend_register_api(char *ext_name, int version, void *api, size_t size)
{
	printf("Function Zend Hello\n");

	zend_hash_add(&ext_api_registry, ext_name, strlen(ext_name) + 1, api, size, NULL);
	
	return 0;
}

