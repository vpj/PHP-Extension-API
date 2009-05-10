#ifndef ZEND_EXT_API_H
#define ZEND_EXT_API_H

#include "zend.h"
#include "zend_hash.h"

ZEND_API int zend_register_api(char *ext_name, int version, void *api, size_t size);
void zend_ext_api_init();
#endif

