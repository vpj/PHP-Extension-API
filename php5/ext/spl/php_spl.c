/*
   +----------------------------------------------------------------------+
   | PHP Version 6                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 1997-2009 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Marcus Boerger <helly@php.net>                              |
   +----------------------------------------------------------------------+
 */

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "php_main.h"
#include "ext/standard/info.h"
#include "php_spl.h"
#include "spl_functions.h"
#include "spl_engine.h"
#include "spl_array.h"
#include "spl_directory.h"
#include "spl_iterators.h"
#include "spl_exceptions.h"
#include "spl_observer.h"
#include "spl_dllist.h"
#include "spl_heap.h"
#include "spl_fixedarray.h"
#include "zend_exceptions.h"
#include "zend_interfaces.h"
#include "ext/standard/php_rand.h"
#include "ext/standard/php_lcg.h"
#include "main/snprintf.h"

#ifdef COMPILE_DL_SPL
ZEND_GET_MODULE(spl)
#endif

ZEND_DECLARE_MODULE_GLOBALS(spl)

/* {{{ PHP_GINIT_FUNCTION
 */
static PHP_GINIT_FUNCTION(spl)
{
	ZVAL_NULL(&spl_globals->autoload_extensions);
	spl_globals->autoload_functions      = NULL;
	spl_globals->autoload_running        = 0;
}
/* }}} */

static zend_class_entry * spl_find_ce_by_name(zend_uchar ztype, zstr name, int len, zend_bool autoload TSRMLS_DC)
{
	zend_class_entry **ce;
	int found;

	if (!autoload) {
		zstr lc_name;

		lc_name = zend_u_str_tolower_dup(ztype, name, len);
		found = zend_u_hash_find(EG(class_table), ztype, lc_name, len +1, (void **) &ce);
		efree(lc_name.v);
	} else {
 		found = zend_u_lookup_class(ztype, name, len, &ce TSRMLS_CC);
 	}
 	if (found != SUCCESS) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Class %v does not exist%s", name, autoload ? " and could not be loaded" : "");
		return NULL;
	}
	
	return *ce;
}

/* {{{ proto array class_parents(object instance [, boolean autoload = true]) U
 Return an array containing the names of all parent classes */
PHP_FUNCTION(class_parents)
{
	zval *obj;
	zend_class_entry *parent_class, *ce;
	zend_bool autoload = 1;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z|b", &obj, &autoload) == FAILURE) {
		RETURN_FALSE;
	}
	
	if (Z_TYPE_P(obj) != IS_OBJECT && Z_TYPE_P(obj) != IS_STRING && Z_TYPE_P(obj) != IS_UNICODE) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "object or string expected");
		RETURN_FALSE;
	}
	
	if (Z_TYPE_P(obj) == IS_STRING || Z_TYPE_P(obj) == IS_UNICODE) {
		if (NULL == (ce = spl_find_ce_by_name(Z_TYPE_P(obj), Z_UNIVAL_P(obj), Z_UNILEN_P(obj), autoload TSRMLS_CC))) {
			RETURN_FALSE;
		}
	} else {
		ce = Z_OBJCE_P(obj);
	}
	
	array_init(return_value);
	parent_class = ce->parent;
	while (parent_class) {
		spl_add_class_name(return_value, parent_class, 0, 0 TSRMLS_CC);
		parent_class = parent_class->parent;
	}
}
/* }}} */

/* {{{ proto array class_implements(mixed what [, bool autoload ]) U
 Return all classes and interfaces implemented by SPL */
PHP_FUNCTION(class_implements)
{
	zval *obj;
	zend_bool autoload = 1;
	zend_class_entry *ce;
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z|b", &obj, &autoload) == FAILURE) {
		RETURN_FALSE;
	}
	if (Z_TYPE_P(obj) != IS_OBJECT && Z_TYPE_P(obj) != IS_STRING && Z_TYPE_P(obj) != IS_UNICODE) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "object or string expected");
		RETURN_FALSE;
	}
	
	if (Z_TYPE_P(obj) == IS_STRING || Z_TYPE_P(obj) == IS_UNICODE) {
		if (NULL == (ce = spl_find_ce_by_name(Z_TYPE_P(obj), Z_UNIVAL_P(obj), Z_UNILEN_P(obj), autoload TSRMLS_CC))) {
			RETURN_FALSE;
		}
	} else {
		ce = Z_OBJCE_P(obj);
	}
	
	array_init(return_value);
	spl_add_interfaces(return_value, ce, 1, ZEND_ACC_INTERFACE TSRMLS_CC);
}
/* }}} */

#define SPL_ADD_CLASS(class_name, z_list, sub, allow, ce_flags) \
	spl_add_classes(spl_ce_ ## class_name, z_list, sub, allow, ce_flags TSRMLS_CC)

#define SPL_LIST_CLASSES(z_list, sub, allow, ce_flags) \
	SPL_ADD_CLASS(AppendIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(ArrayIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(ArrayObject, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(BadFunctionCallException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(BadMethodCallException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(CachingIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(Countable, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(DirectoryIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(DomainException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(EmptyIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(FilesystemIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(FilterIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(GlobIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(InfiniteIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(InvalidArgumentException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(IteratorIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(LengthException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(LimitIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(LogicException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(MultipleIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(NoRewindIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(OuterIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(OutOfBoundsException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(OutOfRangeException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(OverflowException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(ParentIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RangeException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveArrayIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveCachingIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveDirectoryIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveFilterIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveIteratorIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveRegexIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RecursiveTreeIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RegexIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(RuntimeException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SeekableIterator, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplDoublyLinkedList, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplFileInfo, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplFileObject, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplFixedArray, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplHeap, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplMinHeap, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplMaxHeap, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplObjectStorage, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplObserver, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplPriorityQueue, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplQueue, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplStack, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplSubject, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(SplTempFileObject, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(UnderflowException, z_list, sub, allow, ce_flags); \
	SPL_ADD_CLASS(UnexpectedValueException, z_list, sub, allow, ce_flags); \

/* {{{ proto array spl_classes() U
 Return an array containing the names of all clsses and interfaces defined in SPL */
PHP_FUNCTION(spl_classes)
{
	array_init(return_value);
	
	SPL_LIST_CLASSES(return_value, 0, 0, 0)
}
/* }}} */

int spl_autoload(const zstr class_name, const zstr lc_name, int class_name_len, const zstr file_extension TSRMLS_DC) /* {{{ */
{
	char *class_file;
	int class_file_len;
	int dummy = 1;
	zend_file_handle file_handle;
	zend_op_array *new_op_array;
	zval *result = NULL;
	int ret;

	/* UTODO: We want the stream to accept a zstr for opening */
	class_file_len = spprintf(&class_file, 0, "%v%v", lc_name, file_extension);

	ret = php_stream_open_for_zend_ex(class_file, &file_handle, ENFORCE_SAFE_MODE|USE_PATH|STREAM_OPEN_FOR_INCLUDE TSRMLS_CC);

	if (ret == SUCCESS) {
		if (!file_handle.opened_path) {
			file_handle.opened_path = estrndup(class_file, class_file_len);
		}
		if (zend_hash_add(&EG(included_files), file_handle.opened_path, strlen(file_handle.opened_path)+1, (void *)&dummy, sizeof(int), NULL)==SUCCESS) {
			new_op_array = zend_compile_file(&file_handle, ZEND_REQUIRE TSRMLS_CC);
			zend_destroy_file_handle(&file_handle TSRMLS_CC);
		} else {
			new_op_array = NULL;
			zend_file_handle_dtor(&file_handle TSRMLS_CC);
		}
		if (new_op_array) {
			EG(return_value_ptr_ptr) = &result;
			EG(active_op_array) = new_op_array;
			if (!EG(active_symbol_table)) {
				zend_rebuild_symbol_table(TSRMLS_C);
			}

			zend_execute(new_op_array TSRMLS_CC);
	
			destroy_op_array(new_op_array TSRMLS_CC);
			efree(new_op_array);
			if (!EG(exception)) {
				if (EG(return_value_ptr_ptr)) {
					zval_ptr_dtor(EG(return_value_ptr_ptr));
				}
			}

			efree(class_file);
			return zend_u_hash_exists(EG(class_table), IS_UNICODE, lc_name, class_name_len+1);
		}
	}
	efree(class_file);
	return 0;
} /* }}} */

/* {{{ proto void spl_autoload(string class_name [, string file_extensions]) U
 Default implementation for __autoload() */
PHP_FUNCTION(spl_autoload)
{
	zstr class_name, lc_name;
	zstr file_exts = Z_UNIVAL(SPL_G(autoload_extensions));
	int class_name_len, file_exts_len = Z_UNILEN(SPL_G(autoload_extensions)), found = 0;
	zstr copy, pos1, pos2;
	zval **original_return_value = EG(return_value_ptr_ptr);
	zend_op **original_opline_ptr = EG(opline_ptr);
	zend_op_array *original_active_op_array = EG(active_op_array);
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "x|x", &class_name, &class_name_len, &file_exts, &file_exts_len) == FAILURE) {
		RETURN_FALSE;
	}

	copy = pos1 = ezstrndup(IS_UNICODE, file_exts, file_exts_len);
	lc_name = zend_u_str_tolower_dup(IS_UNICODE, class_name, class_name_len);
	while(pos1.v && *pos1.u && !EG(exception)) {
		EG(return_value_ptr_ptr) = original_return_value;
		EG(opline_ptr) = original_opline_ptr;
		EG(active_op_array) = original_active_op_array;
		pos2.u = u_strchr(pos1.u, ',');
		if (pos2.u) {
			*pos2.u = '\0';
		}
		if (spl_autoload(class_name, lc_name, class_name_len, pos1 TSRMLS_CC)) {
			found = 1;
			break; /* loaded */
		}
		if (!pos2.v) {
			pos1.v = NULL;
		} else {
			pos1.u = pos2.u + 1;
		}
	}
	efree(lc_name.v);

	if (copy.v) {
		efree(copy.v);
	}

	EG(return_value_ptr_ptr) = original_return_value;
	EG(opline_ptr) = original_opline_ptr;
	EG(active_op_array) = original_active_op_array;

	if (!found && !SPL_G(autoload_running)) {
		/* For internal errors, we generate E_ERROR, for direct calls an exception is thrown.
		 * The "scope" is determined by an opcode, if it is ZEND_FETCH_CLASS we know function was called indirectly by
		 * the Zend engine.
		 */
		if (active_opline->opcode != ZEND_FETCH_CLASS) {
			zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Class %v could not be loaded", class_name.u);
		} else {
			php_error_docref(NULL TSRMLS_CC, E_ERROR, "Class %v could not be loaded", class_name.u);
		}
	}
} /* }}} */

/* {{{ proto string spl_autoload_extensions([string file_extensions]) U
 Register and return default file extensions for spl_autoload */
PHP_FUNCTION(spl_autoload_extensions)
{
	zstr file_exts;
	int file_exts_len;
	zval *global = &SPL_G(autoload_extensions);

	if (ZEND_NUM_ARGS() > 0) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "x", &file_exts, &file_exts_len) == FAILURE) {
			return;
		}
		zval_dtor(global);
		ZVAL_ZSTRL(global, IS_UNICODE, file_exts, file_exts_len, 1);
	}

	RETURN_ZVAL(global, 1, 0);
} /* }}} */

typedef struct {
	zend_function *func_ptr;
	zval *obj;
	zval *closure;
	zend_class_entry *ce;
} autoload_func_info;

static void autoload_func_info_dtor(autoload_func_info *alfi)
{
	if (alfi->obj) {
		zval_ptr_dtor(&alfi->obj);
	}
	if (alfi->closure) {
		zval_ptr_dtor(&alfi->closure);
	}
}

/* {{{ proto void spl_autoload_call(string class_name) U
 Try all registerd autoload function to load the requested class */
PHP_FUNCTION(spl_autoload_call)
{
	zval *zclass_name, *retval = NULL;
	int class_name_len, func_name_type;
	zstr class_name, func_name, lc_name;
	uint func_name_len;
	ulong dummy;
	HashPosition function_pos;
	autoload_func_info *alfi;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "x", &class_name, &class_name_len) == FAILURE) {
		return;
	}

	MAKE_STD_ZVAL(zclass_name);
	ZVAL_ZSTRL(zclass_name, IS_UNICODE, class_name, class_name_len, 1);
	if (SPL_G(autoload_functions)) {
		int l_autoload_running = SPL_G(autoload_running);
		SPL_G(autoload_running) = 1;
		lc_name = zend_u_str_tolower_dup(IS_UNICODE, class_name, class_name_len);
		zend_hash_internal_pointer_reset_ex(SPL_G(autoload_functions), &function_pos);
		while(zend_hash_has_more_elements_ex(SPL_G(autoload_functions), &function_pos) == SUCCESS) {
			func_name_type = zend_hash_get_current_key_ex(SPL_G(autoload_functions), &func_name, &func_name_len, &dummy, 0, &function_pos);
			zend_hash_get_current_data_ex(SPL_G(autoload_functions), (void **) &alfi, &function_pos);
			zend_u_call_method(alfi->obj ? &alfi->obj : NULL, alfi->ce, &alfi->func_ptr, func_name_type, func_name, func_name_len, &retval, 1, zclass_name, NULL TSRMLS_CC);
			zend_exception_save(TSRMLS_C);
			if (retval) {
				zval_ptr_dtor(&retval);					
			}
			if (zend_u_hash_exists(EG(class_table), IS_UNICODE, lc_name, class_name_len+1)) {
				break;
			}
			zend_hash_move_forward_ex(SPL_G(autoload_functions), &function_pos);
		}
		zend_exception_restore(TSRMLS_C);
		efree(lc_name.v);
		SPL_G(autoload_running) = l_autoload_running;
	} else {
		/* do not use or overwrite &EG(autoload_func) here */
		zend_call_method_with_1_params(NULL, NULL, NULL, "spl_autoload", NULL, zclass_name);
	}
	zval_ptr_dtor(&zclass_name);
} /* }}} */

#define HT_MOVE_TAIL_TO_HEAD(ht)							\
	(ht)->pListTail->pListNext = (ht)->pListHead;			\
	(ht)->pListHead = (ht)->pListTail;						\
	(ht)->pListTail = (ht)->pListHead->pListLast;			\
	(ht)->pListHead->pListNext->pListLast = (ht)->pListHead;\
	(ht)->pListTail->pListNext = NULL;						\
	(ht)->pListHead->pListLast = NULL;

/* {{{ proto bool spl_autoload_register([mixed autoload_function = "spl_autoload" [, throw = true [, prepend = false]]]) U
 Register given function as __autoload() implementation */
PHP_FUNCTION(spl_autoload_register)
{
	char *error = NULL;
	zval zfunc_name, ztmp;
	zval *zcallable = NULL;
	zend_bool do_throw = 1;
	zend_bool prepend = 0;
	zend_function *spl_func_ptr;
	autoload_func_info alfi;
	zval *obj_ptr;
	zend_fcall_info_cache fcc;

	if (zend_parse_parameters_ex(ZEND_PARSE_PARAMS_QUIET, ZEND_NUM_ARGS() TSRMLS_CC, "|zbb", &zcallable, &do_throw, &prepend) == FAILURE) {
		return;
	}

	if (ZEND_NUM_ARGS()) {
		if (Z_TYPE_P(zcallable) == IS_STRING) {
			if (Z_STRLEN_P(zcallable) == sizeof("spl_autoload_call") - 1) {
				if (!zend_binary_strcasecmp(Z_STRVAL_P(zcallable), sizeof("spl_autoload_call"), "spl_autoload_call", sizeof("spl_autoload_call"))) {
					if (do_throw) {
						zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Function spl_autoload_call() cannot be registered");
					}
					RETURN_FALSE;
				}
			}
		} else if (Z_TYPE_P(zcallable) == IS_UNICODE) {
			ZVAL_ASCII_STRINGL(&ztmp, "spl_autoload_call", sizeof("spl_autoload_call")-1, 1);
			if (zend_u_binary_zval_strcmp(&ztmp, zcallable)) {
				if (!zend_binary_strcasecmp(Z_STRVAL_P(zcallable), sizeof("spl_autoload_call"), "spl_autoload_call", sizeof("spl_autoload_call"))) {
					if (do_throw) {
						zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Function spl_autoload_call() cannot be registered");
					}
					zval_dtor(&ztmp);
					RETURN_FALSE;
				}
			}
			zval_dtor(&ztmp);
		}
	
		if (!zend_is_callable_ex(zcallable, NULL, IS_CALLABLE_STRICT, &zfunc_name, &fcc, &error TSRMLS_CC)) {
			alfi.ce = fcc.calling_scope;
			alfi.func_ptr = fcc.function_handler;
			obj_ptr = fcc.object_ptr;
			if (Z_TYPE_P(zcallable) == IS_ARRAY) {
				if (!obj_ptr && alfi.func_ptr && !(alfi.func_ptr->common.fn_flags & ZEND_ACC_STATIC)) {
					if (do_throw) {
						zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Passed array specifies a non static method but no object (%s)", error);
					}
					if (error) {
						efree(error);
					}
					zval_dtor(&zfunc_name);
					RETURN_FALSE;
				}
				else if (do_throw) {
					zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Passed array does not specify %s %smethod (%s)", alfi.func_ptr ? "a callable" : "an existing", !obj_ptr ? "static " : "", error);
				}
				if (error) {
					efree(error);
				}
				zval_dtor(&zfunc_name);
				RETURN_FALSE;
			} else if (Z_TYPE_P(zcallable) == IS_STRING || Z_TYPE_P(zcallable) == IS_UNICODE) {
				if (do_throw) {
					zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Function '%R' not %s (%s)", Z_TYPE_P(zcallable), Z_UNIVAL_P(zcallable), alfi.func_ptr ? "callable" : "found", error);
				}
				if (error) {
					efree(error);
				}
				zval_dtor(&zfunc_name);
				RETURN_FALSE;
			} else {
				if (do_throw) {
					zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Illegal value passed (%s)", error);
				}
				if (error) {
					efree(error);
				}
				zval_dtor(&zfunc_name);
				RETURN_FALSE;
			}
		}
		alfi.closure = NULL;
		alfi.ce = fcc.calling_scope;
		alfi.func_ptr = fcc.function_handler;
		obj_ptr = fcc.object_ptr;

		zend_u_str_tolower(Z_TYPE(zfunc_name), Z_UNIVAL(zfunc_name), Z_UNILEN(zfunc_name));

		if (Z_TYPE_P(zcallable) == IS_OBJECT) {
			zstr lc_name;

			size_t func_name_len = Z_UNISIZE(zfunc_name);

			alfi.closure = zcallable;
			Z_ADDREF_P(zcallable);

			lc_name.v = Z_UNIVAL(zfunc_name).v = erealloc(Z_UNIVAL(zfunc_name).v, func_name_len + 2 + sizeof(zend_object_handle));
			memcpy(lc_name.s + func_name_len, &Z_OBJ_HANDLE_P(zcallable), sizeof(zend_object_handle));
			func_name_len += sizeof(zend_object_handle);
			if (Z_TYPE(zfunc_name) == IS_UNICODE) {
				func_name_len /= sizeof(UChar);
				Z_STRLEN(zfunc_name) = func_name_len;
				lc_name.u[func_name_len] = 0;
			} else {
				Z_STRLEN(zfunc_name) = func_name_len;
				lc_name.s[func_name_len] = '\0';
			}
		}
		if (error) {
			efree(error);
		}
	
		if (SPL_G(autoload_functions) && zend_u_hash_exists(SPL_G(autoload_functions), Z_TYPE(zfunc_name), Z_UNIVAL(zfunc_name), Z_UNILEN(zfunc_name)+1)) {
			if (alfi.closure) {
				Z_DELREF_P(zcallable);
			}
			goto skip;
		}

		if (obj_ptr && !(alfi.func_ptr->common.fn_flags & ZEND_ACC_STATIC)) {
			/* add object id to the hash to ensure uniqueness, for more reference look at bug #40091 */
			zstr lc_name;
			size_t func_name_len = Z_UNISIZE(zfunc_name);
			lc_name.v = Z_UNIVAL(zfunc_name).v = erealloc(Z_UNIVAL(zfunc_name).v, func_name_len + 2 + sizeof(zend_object_handle));
			memcpy(lc_name.s + func_name_len, &Z_OBJ_HANDLE_P(obj_ptr), sizeof(zend_object_handle));
			func_name_len += sizeof(zend_object_handle);
			alfi.obj = obj_ptr;
			Z_ADDREF_P(alfi.obj);
			if (Z_TYPE(zfunc_name) == IS_UNICODE) {
				func_name_len /= sizeof(UChar);
				Z_STRLEN(zfunc_name) = func_name_len;
				lc_name.u[func_name_len] = 0;
			} else {
				Z_STRLEN(zfunc_name) = func_name_len;
				lc_name.s[func_name_len] = '\0';
			}
		} else {
			alfi.obj = NULL;
		}

		if (!SPL_G(autoload_functions)) {
			ALLOC_HASHTABLE(SPL_G(autoload_functions));
			zend_u_hash_init(SPL_G(autoload_functions), 1, NULL, (dtor_func_t) autoload_func_info_dtor, 0, UG(unicode));
		}

		zend_hash_find(EG(function_table), "spl_autoload", sizeof("spl_autoload"), (void **) &spl_func_ptr);

		if (EG(autoload_func) == spl_func_ptr) { /* registered already, so we insert that first */
			autoload_func_info spl_alfi;

			spl_alfi.func_ptr = spl_func_ptr;
			spl_alfi.obj = NULL;
			spl_alfi.ce = NULL;
			spl_alfi.closure = NULL;
			zend_hash_add(SPL_G(autoload_functions), "spl_autoload", sizeof("spl_autoload"), &spl_alfi, sizeof(autoload_func_info), NULL);
			if (prepend && SPL_G(autoload_functions)->nNumOfElements > 1) {
				/* Move the newly created element to the head of the hashtable */
				HT_MOVE_TAIL_TO_HEAD(SPL_G(autoload_functions));
			}
		}

		zend_u_hash_add(SPL_G(autoload_functions), Z_TYPE(zfunc_name), Z_UNIVAL(zfunc_name), Z_UNILEN(zfunc_name)+1, &alfi, sizeof(autoload_func_info), NULL);
		if (prepend && SPL_G(autoload_functions)->nNumOfElements > 1) {
			/* Move the newly created element to the head of the hashtable */
			HT_MOVE_TAIL_TO_HEAD(SPL_G(autoload_functions));
		}
skip:
		zval_dtor(&zfunc_name);
	}

	if (SPL_G(autoload_functions)) {
		zend_hash_find(EG(function_table), "spl_autoload_call", sizeof("spl_autoload_call"), (void **) &EG(autoload_func));
	} else {
		zend_hash_find(EG(function_table), "spl_autoload", sizeof("spl_autoload"), (void **) &EG(autoload_func));
	}
	RETURN_TRUE;
} /* }}} */

/* {{{ proto bool spl_autoload_unregister(mixed autoload_function) U
 Unregister given function as __autoload() implementation */
PHP_FUNCTION(spl_autoload_unregister)
{
	char *error = NULL;
	zval zfunc_name;
	zval *zcallable;
	zstr lc_name;
	size_t lc_name_len;
	int success = FAILURE;
	zend_function *spl_func_ptr;
	zval *obj_ptr;
	zend_fcall_info_cache fcc;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zcallable) == FAILURE) {
		return;
	}

	if (!zend_is_callable_ex(zcallable, NULL, IS_CALLABLE_CHECK_SYNTAX_ONLY, &zfunc_name, &fcc, &error TSRMLS_CC)) {
		zend_throw_exception_ex(spl_ce_LogicException, 0 TSRMLS_CC, "Unable to unregister invalid function (%s)", error);
		if (error) {
			efree(error);
		}
		zval_dtor(&zfunc_name);
		RETURN_FALSE;
	}
	obj_ptr = fcc.object_ptr;
	if (error) {
		efree(error);
	}

	lc_name = zend_u_str_tolower_dup(Z_TYPE(zfunc_name), Z_UNIVAL(zfunc_name), Z_UNILEN(zfunc_name));
	lc_name_len = Z_UNILEN(zfunc_name);

	if (Z_TYPE_P(zcallable) == IS_OBJECT) {
		lc_name_len = Z_UNISIZE(zfunc_name);
		lc_name.v = erealloc(lc_name.v, lc_name_len + 2 + sizeof(zend_object_handle));
		memcpy(lc_name.s + lc_name_len, &Z_OBJ_HANDLE_P(zcallable), sizeof(zend_object_handle));
		lc_name_len += sizeof(zend_object_handle);
		if (Z_TYPE(zfunc_name) == IS_UNICODE) {
			lc_name_len /= sizeof(UChar);
			lc_name.u[lc_name_len] = 0;
		} else {
			lc_name.s[lc_name_len] = '\0';
		}
	}

	if (SPL_G(autoload_functions)) {
		if ((lc_name_len == sizeof("spl_autoload_call")-1) &&
		    (ZEND_U_EQUAL(Z_TYPE(zfunc_name), lc_name, lc_name_len, "spl_autoload_call", sizeof("spl_autoload_call")-1))) {
			/* remove all */
			zend_hash_destroy(SPL_G(autoload_functions));
			FREE_HASHTABLE(SPL_G(autoload_functions));
			SPL_G(autoload_functions) = NULL;
			EG(autoload_func) = NULL;
			success = SUCCESS;
		} else {
			/* remove specific */
			success = zend_u_hash_del(SPL_G(autoload_functions), Z_TYPE(zfunc_name), lc_name, lc_name_len+1);
			if (success != SUCCESS && obj_ptr) {
				if (Z_TYPE(zfunc_name) == IS_UNICODE) {
					lc_name_len *= sizeof(UChar);
				}
				lc_name.v = erealloc(lc_name.v, lc_name_len + 2 + sizeof(zend_object_handle));
				memcpy(lc_name.s + lc_name_len, &Z_OBJ_HANDLE_P(obj_ptr), sizeof(zend_object_handle));
				lc_name_len += sizeof(zend_object_handle);
				if (Z_TYPE(zfunc_name) == IS_UNICODE) {
					lc_name_len /= sizeof(UChar);
					lc_name.u[lc_name_len] = 0;
				} else {
					lc_name.s[lc_name_len] = '\0';
				}
				success = zend_u_hash_del(SPL_G(autoload_functions), Z_TYPE(zfunc_name), lc_name, lc_name_len+1);
			}
		}
	} else if ((lc_name_len == sizeof("spl_autoload")-1) &&
	           (ZEND_U_EQUAL(Z_TYPE(zfunc_name), lc_name, lc_name_len, "spl_autoload", sizeof("spl_autoload")-1))) {
		/* register single spl_autoload() */
		zend_hash_find(EG(function_table), "spl_autoload", sizeof("spl_autoload"), (void **) &spl_func_ptr);

		if (EG(autoload_func) == spl_func_ptr) {
			success = SUCCESS;
			EG(autoload_func) = NULL;
		}
	}

	efree(lc_name.v);
	zval_dtor(&zfunc_name);
	
	RETURN_BOOL(success == SUCCESS);
} /* }}} */

/* {{{ proto false|array spl_autoload_functions() U
 Return all registered __autoload() functionns */
PHP_FUNCTION(spl_autoload_functions)
{
	zend_function *fptr;
	HashPosition function_pos;
	autoload_func_info *alfi;

	if (!EG(autoload_func)) {
		if (zend_hash_find(EG(function_table), ZEND_AUTOLOAD_FUNC_NAME, sizeof(ZEND_AUTOLOAD_FUNC_NAME), (void **) &fptr) == SUCCESS) {
			array_init(return_value);
			add_next_index_stringl(return_value, ZEND_AUTOLOAD_FUNC_NAME, sizeof(ZEND_AUTOLOAD_FUNC_NAME)-1, 1);
			return;
		}
		RETURN_FALSE;
	}

	zend_hash_find(EG(function_table), "spl_autoload_call", sizeof("spl_autoload_call"), (void **) &fptr);

	if (EG(autoload_func) == fptr) {
		array_init(return_value);
		zend_hash_internal_pointer_reset_ex(SPL_G(autoload_functions), &function_pos);
		while(zend_hash_has_more_elements_ex(SPL_G(autoload_functions), &function_pos) == SUCCESS) {
			zend_hash_get_current_data_ex(SPL_G(autoload_functions), (void **) &alfi, &function_pos);
			if (alfi->closure) {
				Z_ADDREF_P(alfi->closure);
				add_next_index_zval(return_value, alfi->closure);
			} else if (alfi->func_ptr->common.scope) {
				zval *tmp;
				MAKE_STD_ZVAL(tmp);
				array_init(tmp);

				if (alfi->obj) {
					Z_ADDREF_P(alfi->obj);
					add_next_index_zval(tmp, alfi->obj);
				} else {
					add_next_index_unicode(tmp, alfi->ce->name.u, 1);
				}
				add_next_index_unicode(tmp, alfi->func_ptr->common.function_name.u, 1);
				add_next_index_zval(return_value, tmp);
			} else {
				add_next_index_unicode(return_value, alfi->func_ptr->common.function_name.u, 1);
			}

			zend_hash_move_forward_ex(SPL_G(autoload_functions), &function_pos);
		}
		return;
	}

	array_init(return_value);
	add_next_index_unicode(return_value, EG(autoload_func)->common.function_name.u, 1);
} /* }}} */

/* {{{ proto string spl_object_hash(object obj) U
 Return hash id for given object */
PHP_FUNCTION(spl_object_hash)
{
	zval *obj;
	char* hash;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &obj) == FAILURE) {
		return;
	}

	hash = emalloc(33);
	php_spl_object_hash(obj, hash TSRMLS_CC);

	RETVAL_STRING(hash, 0);
}
/* }}} */

PHPAPI void php_spl_object_hash(zval *obj, char *result TSRMLS_DC) /* {{{*/
{
	intptr_t hash_handle, hash_handlers;
	char *hex;

	if (!SPL_G(hash_mask_init)) {
		if (!BG(mt_rand_is_seeded)) {
			php_mt_srand(GENERATE_SEED() TSRMLS_CC);
		}

		SPL_G(hash_mask_handle)   = (intptr_t)(php_mt_rand(TSRMLS_C) >> 1);
		SPL_G(hash_mask_handlers) = (intptr_t)(php_mt_rand(TSRMLS_C) >> 1);
		SPL_G(hash_mask_init) = 1;
	}

	hash_handle   = SPL_G(hash_mask_handle)^(intptr_t)Z_OBJ_HANDLE_P(obj);
	hash_handlers = SPL_G(hash_mask_handlers)^(intptr_t)Z_OBJ_HT_P(obj);

	spprintf(&hex, 32, "%016x%016x", hash_handle, hash_handlers);

	strlcpy(result, hex, 33);
	efree(hex);
}
/* }}} */

int spl_build_class_list_string(zval **entry, char **list TSRMLS_DC) /* {{{ */
{
	char *res;
	
	spprintf(&res, 0, "%s, %v", *list, Z_STRVAL_PP(entry));
	efree(*list);
	*list = res;
	return ZEND_HASH_APPLY_KEEP;
} /* }}} */

/* {{{ PHP_MINFO(spl)
 */
PHP_MINFO_FUNCTION(spl)
{
	zval list;
	char *strg;

	php_info_print_table_start();
	php_info_print_table_header(2, "SPL support",        "enabled");

	INIT_PZVAL(&list);
	array_init(&list);
	SPL_LIST_CLASSES(&list, 0, 1, ZEND_ACC_INTERFACE)
	strg = estrdup("");
	zend_hash_apply_with_argument(Z_ARRVAL_P(&list), (apply_func_arg_t)spl_build_class_list_string, &strg TSRMLS_CC);
	zval_dtor(&list);
	php_info_print_table_row(2, "Interfaces", strg + 2);
	efree(strg);

	INIT_PZVAL(&list);
	array_init(&list);
	SPL_LIST_CLASSES(&list, 0, -1, ZEND_ACC_INTERFACE)
	strg = estrdup("");
	zend_hash_apply_with_argument(Z_ARRVAL_P(&list), (apply_func_arg_t)spl_build_class_list_string, &strg TSRMLS_CC);
	zval_dtor(&list);
	php_info_print_table_row(2, "Classes", strg + 2);
	efree(strg);

	php_info_print_table_end();
}
/* }}} */

/* {{{ arginfo */
ZEND_BEGIN_ARG_INFO_EX(arginfo_iterator_to_array, 0, 0, 1)
	ZEND_ARG_OBJ_INFO(0, iterator, Traversable, 0)
	ZEND_ARG_INFO(0, use_keys)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO(arginfo_iterator, 0)
	ZEND_ARG_OBJ_INFO(0, iterator, Traversable, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arginfo_iterator_apply, 0, 0, 2)
	ZEND_ARG_OBJ_INFO(0, iterator, Traversable, 0)
	ZEND_ARG_INFO(0, function)
	ZEND_ARG_ARRAY_INFO(0, args, 1)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_parents, 0, 0, 1)
	ZEND_ARG_INFO(0, instance)
	ZEND_ARG_INFO(0, autoload)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_implements, 0, 0, 1)
	ZEND_ARG_INFO(0, what)
	ZEND_ARG_INFO(0, autoload)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_spl_classes, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_spl_autoload_functions, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_autoload, 0, 0, 1)
	ZEND_ARG_INFO(0, class_name)
	ZEND_ARG_INFO(0, file_extensions)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_autoload_extensions, 0, 0, 0)
	ZEND_ARG_INFO(0, file_extensions)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_autoload_call, 0, 0, 1)
	ZEND_ARG_INFO(0, class_name)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_autoload_register, 0, 0, 0)
	ZEND_ARG_INFO(0, autoload_function)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_autoload_unregister, 0, 0, 1)
	ZEND_ARG_INFO(0, autoload_function)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_object_hash, 0, 0, 1)
	ZEND_ARG_INFO(0, obj)
ZEND_END_ARG_INFO()
/* }}} */

/* {{{ spl_functions
 */
const zend_function_entry spl_functions[] = {
	PHP_FE(spl_classes,             arginfo_spl_classes)
	PHP_FE(spl_autoload,            arginfo_spl_autoload)
	PHP_FE(spl_autoload_extensions, arginfo_spl_autoload_extensions)
	PHP_FE(spl_autoload_register,   arginfo_spl_autoload_register)
	PHP_FE(spl_autoload_unregister, arginfo_spl_autoload_unregister)
	PHP_FE(spl_autoload_functions,  arginfo_spl_autoload_functions)
	PHP_FE(spl_autoload_call,       arginfo_spl_autoload_call)
	PHP_FE(class_parents,           arginfo_class_parents)
	PHP_FE(class_implements,        arginfo_class_implements)
	PHP_FE(spl_object_hash,         arginfo_spl_object_hash)
#ifdef SPL_ITERATORS_H
	PHP_FE(iterator_to_array,       arginfo_iterator_to_array)
	PHP_FE(iterator_count,          arginfo_iterator)
	PHP_FE(iterator_apply,          arginfo_iterator_apply)
#endif /* SPL_ITERATORS_H */
	{NULL, NULL, NULL}
};
/* }}} */

/* {{{ PHP_MINIT_FUNCTION(spl)
 */
PHP_MINIT_FUNCTION(spl)
{
	PHP_MINIT(spl_exceptions)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_iterators)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_array)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_directory)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_dllist)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_heap)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_fixedarray)(INIT_FUNC_ARGS_PASSTHRU);
	PHP_MINIT(spl_observer)(INIT_FUNC_ARGS_PASSTHRU);

	return SUCCESS;
}
/* }}} */

PHP_RINIT_FUNCTION(spl) /* {{{ */
{
	INIT_PZVAL(&SPL_G(autoload_extensions));
	ZVAL_ASCII_STRINGL(&SPL_G(autoload_extensions), ".inc,.php", sizeof(".inc,.php")-1, 1);
	SPL_G(autoload_functions) = NULL;
	SPL_G(autoload_running) = 0;
	SPL_G(autoload_running) = 0;
	SPL_G(hash_mask_init) = 0;
	return SUCCESS;
} /* }}} */

PHP_RSHUTDOWN_FUNCTION(spl) /* {{{ */
{
	if (Z_TYPE(SPL_G(autoload_extensions)) != IS_NULL) {
		zval_dtor(&SPL_G(autoload_extensions));
		ZVAL_NULL(&SPL_G(autoload_extensions));
	}
	if (SPL_G(autoload_functions)) {
		zend_hash_destroy(SPL_G(autoload_functions));
		FREE_HASHTABLE(SPL_G(autoload_functions));
		SPL_G(autoload_functions) = NULL;
	}
	if (SPL_G(hash_mask_init)) {
		SPL_G(hash_mask_init) = 0;
	}
	return SUCCESS;
} /* }}} */

/* {{{ spl_module_entry
 */
zend_module_entry spl_module_entry = {
	STANDARD_MODULE_HEADER,
	"SPL",
	spl_functions,
	PHP_MINIT(spl),
	NULL,
	PHP_RINIT(spl),
	PHP_RSHUTDOWN(spl),
	PHP_MINFO(spl),
	"0.2",
	PHP_MODULE_GLOBALS(spl),
	PHP_GINIT(spl),
	NULL,
	NULL,
	STANDARD_MODULE_PROPERTIES_EX
};
/* }}} */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: fdm=marker
 * vim: noet sw=4 ts=4
 */
