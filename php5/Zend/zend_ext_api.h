#ifndef ZEND_EXT_API_H
#define ZEND_EXT_API_H

#include <zend.h>

ZEND_API int zend_register_api(char *ext_name, int version, void *api, size_t size);
#endif

