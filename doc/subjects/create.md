# Create subject

Create a new subject.

```
POST /subjects
```

Parameters:

- `name` (required) - The name of a subject
- `shortcut` (required) - The shortcut of a subject

## Success

HTTP status: `201 Created`

```json
{
	"id": 6
}
```

## Failure

### Parameters missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"name",
		"shortcut"
	]
}
```

### 3301: A subject with the given name already exists.

HTTP status: `400 Bad Request`

Error code: `3301`
> A subject with the given name already exists.

### 3302: A subject with the given shortcut already exists.

HTTP status: `400 Bad Request`

Error code: `3302`
> A subject with the given shortcut already exists.

### 3300: The subject could not get created.

HTTP status: `409 Conflict`

Error code: `3300`
> The subject could not get created.
