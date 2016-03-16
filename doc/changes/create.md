# Create change

Create a new change.

```
POST /changes
```

Parameters:

- `teacher` (required) - ID of a teacher linked to a change
- `coveringTeacher` (required when type == 1) - ID of a teacher covering the lesson
- `startBy` (required) - Time when a change starts (YYYY-MM-DDT[lesson])
- `endBy` (required) - Time when a change ends (YYYY-MM-DDT[lesson])
- `type` (required) - Type of change (0: cancellation, 1: cover, 2: information)
- `text` (required when type == 2) - Text describing a change
- `reason` - The reason for a change (0: ill, 1: official, 2: on leave)

## Success

HTTP status: `201 Created`

```json
{
	"id": 6
}
```

## Failure

### Parameter(s) missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"teacher",
		"startBy",
		"endBy",
		"type",
		"coveringTeacher",
		"text"
	]
}
```

### 1301/1302: The starting/ending time is formatted badly.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1301",
			"message": "The starting time is formatted badly."
		},
		{
			"code": "1302",
			"message": "The ending time is formatted badly."
		}
	]
}
```

### 1303/1304: The starting/ending time does not exist.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1303",
			"message": "The starting time does not exist."
		},
		{
			"code": "1304",
			"message": "The ending time does not exist."
		}
	]
}
```

### 1305/1306: The (covering) teacher may only contain an integer.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1305",
			"message": "The teacher may only contain an integer."
		},
		{
			"code": "1306",
			"message": "The covering teacher may only contain an integer."
		}
	]
}
```

### 1307: The type is not allowed.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1307",
			"message": "The type is not allowed."
		}
	]
}
```

### 1308/1309: The (covering) teacher does not exist.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1308",
			"message": "The teacher does not exist."
		},
		{
			"code": "1309",
			"message": "The covering teacher does not exist."
		}
	]
}
```

### 1310: The ending time has to be after the start time.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1310",
			"message": "The ending time has to be after the start time."
		}
	]
}
```

### 1311: The reason is not allowed.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1311",
			"message": "The reason is not allowed."
		}
	]
}
```

### 1300: The change could not get created.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "1300",
			"message": "The change could not get created."
		}
	]
}
```
