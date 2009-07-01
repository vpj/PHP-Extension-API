#ifndef ZEND_EXT_API_H
#define ZEND_EXT_API_H

#include "zend.h"
#include "zend_hash.h"

void zend_ext_api_init();
void zend_ext_api_destroy();
int zend_ext_api_callback();

ZEND_API int zend_ext_api_version_toi(char *version_text, uint *version_int);
ZEND_API int zend_ext_api_version_toa(uint version, char ** version_text);

ZEND_API int zend_ext_api_register(char *ext_name, char * version, void *api, size_t size);
ZEND_API int zend_ext_api_register_int_ver(char *ext_name, uint version_int, void *api, size_t size);

ZEND_API int zend_ext_api_exists(char *ext_name, char * version);
ZEND_API int zend_ext_api_exists_int_ver(char *ext_name, uint version);

ZEND_API int zend_ext_api_get(char *ext_name, char * version, void **api);
ZEND_API int zend_ext_api_get_int_ver(char *ext_name, uint version, void **api);

ZEND_API int zend_ext_api_get_latest_version(char *ext_name, uint *version);

ZEND_API int zend_ext_api_set_empty_callback(void (*callback)());
ZEND_API int zend_ext_api_set_callback(char *ext_name, char *version, void (*callback_func)(void *api, char *ext_name, uint version));

#endif
