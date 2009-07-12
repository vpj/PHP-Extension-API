#ifndef ZEND_EXT_API_H
#define ZEND_EXT_API_H

#include "zend.h"
#include "zend_hash.h"

#define EAPI_SET_CALLBACK(ext_name, version, callback) zend_eapi_set_callback(type, module_number, ext_name, version, callback)
#define EAPI_SET_EMPTY_CALLBACK(callback) zend_eapi_set_empty_callback(type, module_number, callback)

#define CALLBACK_FUNC_ARGS int type, int module_number, void *api, char *ext_name, uint version
#define EMPTY_CALLBACK_FUNC_ARGS int type, int module_number

#define EAPI_CALLBACK_FUNCTION_N(callback) callback

#define EAPI_CALLBACK_FUNCTION(callback) void EAPI_CALLBACK_FUNCTION_N(callback)(CALLBACK_FUNC_ARGS)
#define EAPI_EMPTY_CALLBACK_FUNCTION(callback) void EAPI_CALLBACK_FUNCTION_N(callback)(EMPTY_CALLBACK_FUNC_ARGS)

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

ZEND_API int zend_eapi_set_empty_callback(int type, int module_number, void (*callback)(EMPTY_CALLBACK_FUNC_ARGS));
ZEND_API int zend_eapi_set_callback(int type, int module_number, char *ext_name, char *version, void (*callback_func)(CALLBACK_FUNC_ARGS));

#endif
