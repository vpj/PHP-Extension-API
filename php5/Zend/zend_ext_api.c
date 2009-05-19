#include "zend.h"
#include "zend_ext_api.h"

/* Stores all the APIs */
HashTable ext_api_registry;

/* Wrapper structure for APIs */
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
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *)api;
	
	/* Free memory */
	pefree(ext_api->ext_name, ext_api_registry.persistent);
	pefree(ext_api->version_text, ext_api_registry.persistent);
	pefree(ext_api->api, ext_api_registry.persistent);
	
	/* The hash entry will be freed by the hash table */
	/* pefree(ext_api, ext_api_registry.persistent); */
}

void zend_ext_api_destroy()
{
	/* Destroy the hash table */
    /* zend_hash_graceful_reverse_destroy(&module_registry); We don't need this at the moment*/
	zend_hash_destroy(&ext_api_registry);
}

void zend_ext_api_init()
{
    if(zend_hash_init_ex(&ext_api_registry, 10, NULL, zend_ext_api_free_api, /* persistent */1, 0) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Unable to initialize the HashTable\n");
#endif
	}
}

zend_ext_api_extension * zend_ext_api_create(HashTable *ht, char *ext_name, uint version, char *version_text, void *api, size_t size)
{
	zend_ext_api_extension *ext_api = (zend_ext_api_extension *)pemalloc(sizeof(zend_ext_api_extension), ht->persistent);

	if(!ext_api)
	{
		return NULL;
	}
 
    ext_api->version = version;
	ext_api->version_text = strdup(version_text);
	ext_api->size = size;
	ext_api->ext_name = strdup(ext_name);
	ext_api->api = (void *)pemalloc(size, ht->persistent);
	
	if(!(ext_api->api))
	{
		return NULL;
	}

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

	if(!version_text)
	{
		return FAILURE;
	}

	sprintf(*version_text, "%u", version);

	return SUCCESS;
}

int zend_ext_api_hash_name(HashTable *ht, char *ext_name, uint version, char ** hash_name)
{
	*hash_name = pemalloc(strlen(ext_name) + 10, ht->persistent); 

	if(!hash_name)
	{
		return FAILURE;
	}

	sprintf(*hash_name, "%s-%x", ext_name, version);

	return SUCCESS;
}

ZEND_API int zend_ext_api_register(char *ext_name, char * version, void *api, size_t size)
{
	uint vi;
	char *hash_name;
	int r;
	zend_ext_api_extension *ext_api;

	/* Parse the version number */
	if(zend_ext_api_version(version, &vi) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Invalid version format\n");
#endif
		return FAILURE;
	}

	/* Extensions are added to the hash table by hash_name, which is ext_name + '-' + version */
	if(zend_ext_api_hash_name(&ext_api_registry, ext_name, vi, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}

	/* Wrap the api structure */
	ext_api = zend_ext_api_create(&ext_api_registry, ext_name, vi, version, api, size);
	if(!ext_api)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Memory allocation error\n");
#endif
		return FAILURE;
	}

	r = zend_hash_add(&ext_api_registry, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_ext_api_extension), NULL);
	
	/* Clear ext_api memory */
	pefree(ext_api, ext_api_registry.persistent);
	/* Clear hash_name memory */
	pefree(hash_name, ext_api_registry.persistent);

	return r;
}

int zend_ext_api_register_int_ver(char *ext_name, uint version, void *api, size_t size)
{
	char *version_text;
	char *hash_name;
	int r;
	zend_ext_api_extension *ext_api;

	if(zend_ext_api_ver_str(&ext_api_registry, version, &version_text) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Cannot convert version number to a string\n");
#endif
		return FAILURE;
	}

	if(zend_ext_api_hash_name(&ext_api_registry, ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}
	
	/* Wrap the api structure */
	ext_api = zend_ext_api_create(&ext_api_registry, ext_name, version, version_text, api, size);
	if(!ext_api)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Memory allocation error\n");
#endif
		return FAILURE;
	}

	r = zend_hash_add(&ext_api_registry, hash_name, strlen(hash_name) + 1, ext_api, sizeof(zend_ext_api_extension), NULL);
	
	/* Clear ext_api memory */
	pefree(ext_api, ext_api_registry.persistent);
	/* Clear hash_name memory */
	pefree(hash_name, ext_api_registry.persistent);

	return r;
}

int zend_ext_api_exists_int_ver(char *ext_name, uint version)
{
	char *hash_name;
	int r;

	if(zend_ext_api_hash_name(&ext_api_registry, ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}
	
	r = zend_hash_exists(&ext_api_registry, hash_name, strlen(hash_name) + 1);
	
	/* Clear hash_name memory */
	pefree(hash_name, ext_api_registry.persistent);

	return r;
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
	zend_ext_api_extension *ext_api;
	
	if(zend_ext_api_hash_name(&ext_api_registry, ext_name, version, &hash_name) == FAILURE)
	{
#if ZEND_DEBUG
		ZEND_PUTS("Error creating the hash name\n");
#endif
		return FAILURE;
	}
	
	if(zend_hash_find(&ext_api_registry, hash_name, strlen(hash_name) + 1, (void **)(&ext_api)) == FAILURE)
	{
		return FAILURE;
	}

	/* WARNING: Should the api be passed back or a copy? */
	*api = ext_api->api;

	/* Clear hash_name memory */
	pefree(hash_name, ext_api_registry.persistent);

	return SUCCESS;
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

