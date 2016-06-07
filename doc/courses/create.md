# Create course

Create a new course.

```
POST /courses
```

Parameters:

- `name` (required) - The name of a course

## Success

HTTP status: `201 Created`

```json
{
	"id": 6
}
```

## Failure

### Name missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"name"
	]
}
```

### 2301: A course with the given name already exists.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "2301",
			"message": "A course with the given name already exists."
		}
	]
}
```

### 2300: The course could not get created.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "2300",
			"message": "The course could not get created."
		}
	]
}
```
