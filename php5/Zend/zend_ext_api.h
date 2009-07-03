#ifndef ZEND_EXT_API_H
#define ZEND_EXT_API_H

#include "zend.h"
#include "zend_hash.h"

void zend_eapi_init();
void zend_eapi_destroy();
int zend_eapi_callback();

ZEND_API int zend_eapi_version_toi(char *version_text, uint *version_int);
ZEND_API int zend_eapi_version_toa(uint version, char ** version_text);

ZEND_API int zend_eapi_register(char *ext_name, char * version, void *api, size_t size);
ZEND_API int zend_eapi_register_int_ver(char *ext_name, uint version_int, void *api, size_t size);

ZEND_API int zend_eapi_exists(char *ext_name, char * version);
ZEND_API int zend_eapi_exists_int_ver(char *ext_name, uint version);

ZEND_API int zend_eapi_get(char *ext_name, char * version, void **api);
ZEND_API int zend_eapi_get_int_ver(char *ext_name, uint version, void **api);

ZEND_API int zend_eapi_get_latest_version(char *ext_name, uint *version);

ZEND_API int zend_eapi_set_empty_callback(void (*callback)());
ZEND_API int zend_eapi_set_callback(char *ext_name, char *version, void (*callback_func)(void *api, char *ext_name, uint version));

#endif
