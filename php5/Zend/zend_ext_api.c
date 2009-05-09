#include <zend.h>
#include <zend_ext_api.h>

ZEND_API int zend_register_api(char *ext_name, int version, void *api, size_t size)
{
	printf("Function Zend Hello\n");

	return 0;
}

