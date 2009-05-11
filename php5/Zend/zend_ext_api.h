#ifndef ZEND_EXT_API_H
#define ZEND_EXT_API_H

#include "zend.h"
#include "zend_hash.h"

void zend_ext_api_init();
ZEND_API int zend_ext_api_register(char *ext_name, int version, void *api, size_t size);
ZEND_API int zend_ext_api_exists(char *ext_name, int version);
ZEND_API int zend_ext_api_get(char *ext_name, int version, void **api);
#endif
