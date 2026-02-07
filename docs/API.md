# Laravel Workflow API Documentation

Complete API reference for the Laravel Workflow package.

## Base URL
```
https://your-app.com/api/workflows
```

## Authentication

All API endpoints require authentication using Laravel Sanctum bearer tokens.

Include the token in the `Authorization` header:
```
Authorization: Bearer your-token-here
```

## Endpoints

### Workflows

#### List Workflows
```http
GET /
```

Get a paginated list of active workflows.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `type` | string | Filter by workflow type (e.g., "onboarding") |
| `include_inactive` | boolean | Include inactive workflows (default: false) |
| `include_steps` | boolean | Include workflow steps (default: false) |
| `per_page` | integer | Items per page (default: 15) |

**Example Request:**
```bash
curl -X GET "https://your-app.com/api/workflows?type=onboarding&include_steps=true" \
  -H "Authorization: Bearer your-token"
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Employee Onboarding",
      "description": "Complete onboarding process",
      "type": "onboarding",
      "is_active": true,
      "steps_count": 5,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

---

#### Get Workflow
```http
GET /{workflow}
```

Retrieve a specific workflow with its steps.

**Example Request:**
```bash
curl -X GET "https://your-app.com/api/workflows/1" \
  -H "Authorization: Bearer your-token"
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "name": "Employee Onboarding",
    "description": "Complete onboarding process",
    "type": "onboarding",
    "is_active": true,
    "steps": [
      {
        "id": 1,
        "order": 1,
        "title": "Complete Profile",
        "description": "Fill out your details",
        "type": "form"
      }
    ]
  }
}
```

---

#### Check Workflow Availability
```http
GET /{workflow}/availability
```

Check if the current user can start a workflow (considering dependencies).

**Example Response:**
```json
{
  "available": true,
  "missing_dependencies": []
}
```

Or if dependencies are not met:
```json
{
  "available": false,
  "missing_dependencies": [
    {
      "id": 2,
      "name": "Interview Process"
    }
  ]
}
```

---

### Workflow Instances

#### Start Workflow
```http
POST /instances
```

Create a new workflow instance for the authenticated user.

**Request Body:**
```json
{
  "workflow_id": 1,
  "metadata": {
    "department": "engineering",
    "hire_date": "2024-01-15"
  }
}
```

**Example Request:**
```bash
curl -X POST "https://your-app.com/api/workflows/instances" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "workflow_id": 1,
    "metadata": {
      "department": "engineering"
    }
  }'
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "workflow_id": 1,
    "user_id": 1,
    "current_step_id": 1,
    "status": "in_progress",
    "started_at": "2024-01-01T00:00:00.000000Z",
    "workflow": {...},
    "current_step": {...}
  }
}
```

---

#### Get Instance Details
```http
GET /instances/{instance}
```

Get current step details and workflow status.

**Example Response:**
```json
{
  "instance_id": 1,
  "status": "in_progress",
  "current_step": {
    "id": 2,
    "title": "Complete Profile",
    "description": "Fill out your employee details",
    "type": "form",
    "configuration": {
      "fields": ["name", "email", "phone"]
    },
    "can_view": true,
    "can_complete": true
  },
  "workflow": {
    "id": 1,
    "name": "Employee Onboarding",
    "type": "onboarding"
  }
}
```

---

#### Complete Step
```http
POST /instances/{instance}/complete-step
```

Complete the current step and advance to the next.

**Request Body:**
```json
{
  "data": {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "555-1234"
  }
}
```

**Example Request:**
```bash
curl -X POST "https://your-app.com/api/workflows/instances/1/complete-step" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "data": {
      "answer": "yes",
      "score": 85
    }
  }'
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "current_step_id": 3,
    "status": "in_progress",
    ...
  }
}
```

---

#### List User Workflows
```http
GET /instances/user
```

Get all workflow instances for the authenticated user.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status (pending, in_progress, paused, completed, abandoned) |
| `workflow_type` | string | Filter by workflow type |
| `per_page` | integer | Items per page (default: 15) |

**Example Request:**
```bash
curl -X GET "https://your-app.com/api/workflows/instances/user?status=in_progress" \
  -H "Authorization: Bearer your-token"
```

---

#### Pause Workflow
```http
POST /instances/{instance}/pause
```

Pause an in-progress workflow.

---

#### Resume Workflow
```http
POST /instances/{instance}/resume
```

Resume a paused workflow.

---

#### Abandon Workflow
```http
POST /instances/{instance}/abandon
```

Abandon a workflow permanently.

---

### Admin Endpoints

All admin endpoints require the `admin` middleware.

#### Create Workflow
```http
POST /admin/workflows/workflows
```

**Request Body:**
```json
{
  "name": "Employee Onboarding",
  "description": "Complete onboarding process for new employees",
  "type": "onboarding",
  "is_active": true
}
```

---

#### Update Workflow
```http
PUT /admin/workflows/workflows/{workflow}
```

**Request Body:**
```json
{
  "name": "Updated Workflow Name",
  "is_active": false
}
```

---

#### Delete Workflow
```http
DELETE /admin/workflows/workflows/{workflow}
```

---

#### Create Step
```http
POST /admin/workflows/steps
```

**Request Body:**
```json
{
  "workflow_id": 1,
  "order": 1,
  "title": "Complete Profile",
  "description": "Fill out your employee profile",
  "type": "form",
  "configuration": {
    "fields": ["name", "email", "phone"]
  },
  "can_complete_roles": ["employee"]
}
```

---

#### Reorder Steps
```http
POST /admin/workflows/steps/reorder
```

**Request Body:**
```json
{
  "steps": [
    {"id": 2, "order": 1},
    {"id": 1, "order": 2},
    {"id": 3, "order": 3}
  ]
}
```

---

#### Create Action
```http
POST /admin/workflows/actions
```

**Email Action Example:**
```json
{
  "workflow_step_id": 1,
  "type": "email",
  "trigger": "on_step_complete",
  "configuration": {
    "to": "{{user.email}}",
    "subject": "Welcome to {{workflow.name}}!",
    "template": "emails.workflow.welcome"
  }
}
```

**Webhook Action Example:**
```json
{
  "workflow_step_id": 1,
  "type": "webhook",
  "trigger": "on_step_complete",
  "configuration": {
    "url": "https://api.example.com/webhook",
    "method": "POST",
    "payload": {
      "user_id": "{{user.id}}",
      "workflow_id": "{{workflow.id}}"
    }
  }
}
```

---

## Error Responses

All endpoints return standard error responses:

**Validation Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "workflow_id": [
      "The workflow id field is required."
    ]
  }
}
```

**Unauthorized (401):**
```json
{
  "message": "Unauthenticated."
}
```

**Forbidden (403):**
```json
{
  "message": "User does not have permission to complete this step"
}
```

**Not Found (404):**
```json
{
  "message": "No query results for model [Workflow] 1"
}
```

---

## Template Variables

Actions support dynamic template variables:

| Variable | Description |
|----------|-------------|
| `{{user.id}}` | Current user's ID |
| `{{user.name}}` | Current user's name |
| `{{user.email}}` | Current user's email |
| `{{workflow.id}}` | Workflow ID |
| `{{workflow.name}}` | Workflow name |
| `{{instance.id}}` | Workflow instance ID |

---

## Rate Limiting

API endpoints are rate-limited according to your Laravel configuration. Default is 60 requests per minute per authenticated user.

---

## Pagination

List endpoints return paginated results with the following structure:
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 15,
    "to": 15,
    "total": 45
  }
}
```