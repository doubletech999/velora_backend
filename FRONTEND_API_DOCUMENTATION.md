# API Documentation for Frontend - Firebase Notifications

## 🔐 Authentication Required
All endpoints require authentication using Bearer token in the Authorization header.

---

## 📤 Update FCM Token

### Endpoint
```
POST /api/notifications/update-token
```

### Headers
```
Authorization: Bearer {user_auth_token}
Content-Type: application/json
```

### Request Body
```json
{
  "fcm_token": "string (required)"
}
```

### Success Response (200)
```json
{
  "success": true,
  "message": "FCM token updated successfully"
}
```

### Error Response (422)
```json
{
  "success": false,
  "message": "Validation errors",
  "errors": {
    "fcm_token": [
      "The fcm token field is required."
    ]
  }
}
```

### Error Response (401)
```json
{
  "message": "Unauthenticated."
}
```

---

## 📨 Notification Types

### 1. New Route/Camping Notification

**Triggered when:** Admin creates a new route or camping site

**Notification Payload:**
```json
{
  "notification": {
    "title": "New Route",  // or "New Camping"
    "body": "Route Name - Description..."
  },
  "data": {
    "type": "new_route_camping",
    "site_id": "123",
    "site_type": "route",  // or "camping"
    "site_name": "Route Name"
  }
}
```

**Action:** Navigate to site details page with `site_id`

---

### 2. Trip Accepted Notification

**Triggered when:** User's trip booking is confirmed/accepted

**Notification Payload:**
```json
{
  "notification": {
    "title": "Trip Accepted",
    "body": "Your trip \"Trip Name\" has been accepted!"
  },
  "data": {
    "type": "trip_accepted",
    "trip_id": "456",
    "trip_name": "Trip Name"
  }
}
```

**Action:** Navigate to trip details page with `trip_id`

---

## 🔄 Implementation Flow

### 1. On App Launch / Login
```dart
// Get FCM Token
String? fcmToken = await FirebaseMessaging.instance.getToken();

// Send to Backend
POST /api/notifications/update-token
{
  "fcm_token": fcmToken
}
```

### 2. On Token Refresh
```dart
FirebaseMessaging.instance.onTokenRefresh.listen((newToken) {
  // Resend new token to Backend
  POST /api/notifications/update-token
  {
    "fcm_token": newToken
  }
});
```

### 3. Handle Incoming Notifications
```dart
// Foreground
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  // Show local notification
  // Handle data: message.data
});

// Background
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  // Handle data: message.data
}

// Notification Tap
FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  // Navigate based on message.data['type']
});
```

---

## 📋 Data Fields Reference

### Notification Data Object
| Field | Type | Description |
|-------|------|-------------|
| `type` | string | Notification type: `new_route_camping` or `trip_accepted` |
| `site_id` | string | Site ID (for route/camping notifications) |
| `site_type` | string | `route` or `camping` |
| `site_name` | string | Site name |
| `trip_id` | string | Trip ID (for trip accepted notifications) |
| `trip_name` | string | Trip name |

---

## 🧪 Testing

### Test FCM Token Update
```bash
curl -X POST https://your-api.com/api/notifications/update-token \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"fcm_token": "test_token_123"}'
```

### Test Notifications
1. Create a new route/camping from admin panel
2. Accept a trip booking from API or admin panel
3. Check device for notification

---

## ⚠️ Important Notes

1. **Token must be updated** before user can receive notifications
2. **Token may refresh** - always listen to `onTokenRefresh` and resend
3. **Handle all notification states**: foreground, background, terminated
4. **Navigate appropriately** based on `data.type` field
5. **Test on real devices** - emulators may not receive notifications properly

