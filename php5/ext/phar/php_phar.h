/*
  +----------------------------------------------------------------------+
  | phar php single-file executable PHP extension                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 2005-2009 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Gregory Beaver <cellog@php.net>                             |
  |          Marcus Boerger <helly@php.net>                              |
  +----------------------------------------------------------------------+
*/

/* $Id: php_phar.h,v 1.16.2.3 2008/12/31 11:15:42 sebastian Exp $ */

#ifndef PHP_PHAR_H
#define PHP_PHAR_H

#define PHP_PHAR_VERSION "2.0.0-dev"

#include "ext/standard/basic_functions.h"
extern zend_module_entry phar_module_entry;
#define phpext_phar_ptr &phar_module_entry

#ifdef PHP_WIN32
#define PHP_PHAR_API __declspec(dllexport)
#else
#define PHP_PHAR_API
#endif

#endif /* PHP_PHAR_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
