/*
   +----------------------------------------------------------------------+
   | Zend Engine                                                          |
   +----------------------------------------------------------------------+
   | Copyright (c) 1998-2009 Zend Technologies Ltd. (http://www.zend.com) |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
   | Authors: Andi Gutmans <andi@zend.com>                                |
   |          Zeev Suraski <zeev@zend.com>                                |
   +----------------------------------------------------------------------+
*/

/* $Id$ */

#include "zend.h"
#include <zend_language_parser.h>
#include "zend_compile.h"
#include "zend_highlight.h"
#include "zend_ptr_stack.h"
#include "zend_globals.h"
#include "zend_operators.h"

ZEND_API void zend_html_putc(char c) /* {{{ */
{
	switch (c) {
		case '\n':
			ZEND_PUTS("<br />");
			break;
		case '<':
			ZEND_PUTS("&lt;");
			break;
		case '>':
			ZEND_PUTS("&gt;");
			break;
		case '&':
			ZEND_PUTS("&amp;");
			break;
		case ' ':
			ZEND_PUTS("&nbsp;");
			break;
		case '\t':
			ZEND_PUTS("&nbsp;&nbsp;&nbsp;&nbsp;");
			break;
		default:
			ZEND_PUTC(c);
			break;
	}
}
/* }}} */

ZEND_API void zend_html_puts(const char *s, uint len TSRMLS_DC) /* {{{ */
{
	const char *ptr=s, *end=s+len;

	while (ptr<end) {
		if (*ptr==' ') {
			do {
				zend_html_putc(*ptr);
			} while ((++ptr < end) && (*ptr==' '));
		} else {
			zend_html_putc(*ptr++);
		}
	}
}
/* }}} */

ZEND_API void zend_highlight(zend_syntax_highlighter_ini *syntax_highlighter_ini TSRMLS_DC) /* {{{ */
{
	zval token;
	int token_type;
	char *last_color = syntax_highlighter_ini->highlight_html;
	char *next_color;

	CG(literal_type) = IS_STRING;
	zend_printf("<code>");
	zend_printf("<span style=\"color: %s\">\n", last_color);
	/* highlight stuff coming back from zendlex() */
	Z_TYPE(token) = 0;
	while ((token_type=lex_scan(&token TSRMLS_CC))) {
		switch (token_type) {
			case T_INLINE_HTML:
				next_color = syntax_highlighter_ini->highlight_html;
				break;
			case T_COMMENT:
			case T_DOC_COMMENT:
				next_color = syntax_highlighter_ini->highlight_comment;
				break;
			case T_OPEN_TAG:
			case T_OPEN_TAG_WITH_ECHO:
				next_color = syntax_highlighter_ini->highlight_default;
				break;
			case T_CLOSE_TAG:
				next_color = syntax_highlighter_ini->highlight_default;
				break;
			case '"':
			case T_ENCAPSED_AND_WHITESPACE:
			case T_CONSTANT_ENCAPSED_STRING:
				next_color = syntax_highlighter_ini->highlight_string;
				break;
			case T_WHITESPACE:
				zend_html_puts(LANG_SCNG(yy_text), LANG_SCNG(yy_leng) TSRMLS_CC);  /* no color needed */
				Z_TYPE(token) = 0;
				continue;
				break;
			default:
				if (Z_TYPE(token) == 0) {
					next_color = syntax_highlighter_ini->highlight_keyword;
				} else {
					next_color = syntax_highlighter_ini->highlight_default;
				}
				break;
		}

		if (last_color != next_color) {
			if (last_color != syntax_highlighter_ini->highlight_html) {
				zend_printf("</span>");
			}
			last_color = next_color;
			if (last_color != syntax_highlighter_ini->highlight_html) {
				zend_printf("<span style=\"color: %s\">", last_color);
			}
		}

		zend_html_puts(LANG_SCNG(yy_text), LANG_SCNG(yy_leng) TSRMLS_CC);

		if (Z_TYPE(token) == IS_STRING ||
		    Z_TYPE(token) == IS_UNICODE) {
			switch (token_type) {
				case T_OPEN_TAG:
				case T_OPEN_TAG_WITH_ECHO:
				case T_CLOSE_TAG:
				case T_WHITESPACE:
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;
				default:
					efree(Z_UNIVAL(token).v);
					break;
			}
		} else if (token_type == T_END_HEREDOC) {
			efree(Z_UNIVAL(token).v);
		}
		Z_TYPE(token) = 0;
	}

	if (last_color != syntax_highlighter_ini->highlight_html) {
		zend_printf("</span>\n");
	}
	zend_printf("</span>\n");
	zend_printf("</code>");
}
/* }}} */

ZEND_API void zend_strip(TSRMLS_D) /* {{{ */
{
	zval token;
	int token_type;
	int prev_space = 0;

	CG(literal_type) = IS_STRING;
	Z_TYPE(token) = 0;
	while ((token_type=lex_scan(&token TSRMLS_CC))) {
		switch (token_type) {
			case T_WHITESPACE:
				if (!prev_space) {
					zend_write(" ", sizeof(" ") - 1);
					prev_space = 1;
				}
						/* lack of break; is intentional */
			case T_COMMENT:
			case T_DOC_COMMENT:
				Z_TYPE(token) = 0;
				continue;

			case T_END_HEREDOC:
				zend_write(LANG_SCNG(yy_text), LANG_SCNG(yy_leng));
				efree(Z_STRVAL(token));
				/* read the following character, either newline or ; */
				if (lex_scan(&token TSRMLS_CC) != T_WHITESPACE) {
					zend_write(LANG_SCNG(yy_text), LANG_SCNG(yy_leng));
  				}
				zend_write("\n", sizeof("\n") - 1);
				prev_space = 1;
				Z_TYPE(token) = 0;
				continue;

			default:
				zend_write(LANG_SCNG(yy_text), LANG_SCNG(yy_leng));
				break;
		}

		if (Z_TYPE(token) == IS_STRING ||
		    Z_TYPE(token) == IS_UNICODE) {
			switch (token_type) {
				case T_OPEN_TAG:
				case T_OPEN_TAG_WITH_ECHO:
				case T_CLOSE_TAG:
				case T_WHITESPACE:
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;

				default:
					efree(Z_UNIVAL(token).v);
					break;
			}
		}
		prev_space = Z_TYPE(token) = 0;
	}
}
/* }}} */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
