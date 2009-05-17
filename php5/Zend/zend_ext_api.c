#include "zend.h"
#include "zend_ext_api.h"

HashTable ext_api_registry;
/* TODO: Check if memory allocations fail */

struct _zend_ext_api_extension {
	char *ext_name;
	uint version;
	char *version_text;
	size_t size;
	void *api;
};

typedef struct _zend_ext_api_extension zend_ext_api_extension;

void zend_ext_api_free_api(void *api)
{
	//zend_ext_api_extension *ext_api = (zend_ext_api_extension *)api;
	/* TODO: Free memory */
}

void zend_ext_api_destroy()
{
}

void zend_ext_api_init()
{
    zend_hash_init_ex(&ext_api_registry, 10, NULL, zend_ext_api_free_api, 1, 0);
}

/* TODO: Parse the HashTable as a parameter */
zend_ext_api_extension * zend_ext_api_create(HashTable *ht, char *ext_name, uint version, char *version_text, void *api, size_t size)
{
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *) /*malloc(sizeof(zend_ext_api_extension));*/pemalloc(sizeof(zend_ext_api_extension), ht->persistent);
 
    ext_api->version = version;
	ext_api->version_text = strdup(version_text);
	ext_api->size = size;
	ext_api->ext_name = strdup(ext_name);
	ext_api->api = (void *)/*malloc(size);*/pemalloc(size, ht->persistent);
    memcpy(ext_api->api, api, size);

	return ext_api;
}

int zend_ext_api_version(char *version_text, uint *version_int)
{
	uint t;
	int i;
	uint mul = 256 * 256 * 256;

	*version_int = 0;
    
	t = 0;
	for(i = 0; 1; i++)
	{
	    if(isdigit(version_text[i]))
		{
			t *= 10;
			t += version_text[i] - '0';

			if(t >= 256)
			{
				return FAILURE;
			}
		}
		else if(version_text[i] == '.' || version_text[i] == '\0')
		{
			*version_int += t * mul;
			mul /= 256;
			t = 0;

			if(version_text[i] == '\0')
			{
				return SUCCESS;
			}

			if(mul <= 0)
			{
				return FAILURE;
			}
		}

	}
}

int zend_ext_api_ver_str(HashTable *ht, uint version, char ** version_text)
{
	*version_text = pemalloc(14, ht->persistent);

	sprintf(*version_text, "%u", version);

	return SUCCESS;
}

int zend_ext_api_hash_name(HashTable *ht, char *ext_name, uint version, char ** hash_name)
{
	*hash_name = pemalloc(strlen(ext_name) + 10, ht->persistent); /*malloc(100);*/ /* TODO: Change this to strlen (ext_name) and free the memory after use */

	sprintf(*hash_name, "%s-%x", ext_name, version);

	return SUCCESS;
}

ZEND_API int zend_ext_api_register(char *ext_name, char * version, void *api, size_t size)
{
	uint vi;
	char *hash_name;
	int r;

	if(zend_ext_api_version(version, &vi) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return FAILURE;
	}

	zend_ext_api_hash_name(&ext_api_registry, ext_name, vi, &hash_name);
	/* TODO: Clear hash_name memory */
	zend_ext_api_extension *ext_api = zend_ext_api_create(&ext_api_registry, ext_name, vi, version, api, size);
	/* Check errors when creating extension */

	r = zend_hash_add(&ext_api_registry, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_ext_api_extension), NULL);
	/* TODO: Clear ext_api memory */

	return r;
}

int zend_ext_api_register_int_ver(char *ext_name, uint version, void *api, size_t size)
{
	char *version_text;
	char *hash_name;
	int r;

	zend_ext_api_ver_str(&ext_api_registry, version, &version_text);
	zend_ext_api_hash_name(&ext_api_registry, ext_name, version, &hash_name);
	/* TODO: Clear hash_name memory */
	zend_ext_api_extension *ext_api = zend_ext_api_create(&ext_api_registry, ext_name, version, version_text, api, size);
	/* Check errors when creating extension */

	r = zend_hash_add(&ext_api_registry, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_ext_api_extension), NULL);
	/* TODO: Clear ext_api memory */

	return r;
}

int zend_ext_api_exists_int_ver(char *ext_name, uint version)
{
	char *hash_name;
	
	zend_ext_api_hash_name(&ext_api_registry, ext_name, version, &hash_name);
	/* TODO: Clear hash_name memory */
	return zend_hash_exists(&ext_api_registry, hash_name, strlen(hash_name) + 1);
}

ZEND_API int zend_ext_api_exists(char *ext_name, char *version)
{
	uint vi;

    if(zend_ext_api_version(version, &vi) == FAILURE)
    {
#if ZEND_DEBUG
        ZEND_PUTS("Invalid version format\n");
#endif
        return FAILURE;
    }
							
	return zend_ext_api_exists_int_ver(ext_name, vi);
}

int zend_ext_api_get_int_ver(char *ext_name, uint version, void **api)
{
	char *hash_name;
	
	zend_ext_api_hash_name(&ext_api_registry, ext_name, version, &hash_name);
	/* TODO: Clear hash_name memory */
	zend_ext_api_extension *ext_api;
	int r = zend_hash_find(&ext_api_registry, hash_name, strlen(hash_name) + 1, (void **)(&ext_api));
	*api = ext_api->api;

	return r;
}

ZEND_API int zend_ext_api_get(char *ext_name, char *version, void **api)
{
	uint vi;

    if(zend_ext_api_version(version, &vi) == FAILURE)
    {
#if ZEND_DEBUG
        ZEND_PUTS("Invalid version format\n");
#endif
        return FAILURE;
    }
							
	return zend_ext_api_get_int_ver(ext_name, vi, api);
}

