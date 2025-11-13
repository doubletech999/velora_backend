# دليل تكامل Firebase Notifications للفرونت إند (Flutter/Android/iOS)

## 📱 نظرة عامة

يجب على التطبيق (Flutter/Android/iOS) القيام بالآتي:
1. الحصول على FCM Token من Firebase
2. إرسال Token إلى Backend عند تسجيل الدخول أو فتح التطبيق
3. التعامل مع الإشعارات الواردة من Backend

---

## 🔑 1. الحصول على FCM Token

### Flutter (firebase_messaging):
```dart
import 'package:firebase_messaging/firebase_messaging.dart';

FirebaseMessaging messaging = FirebaseMessaging.instance;

// الحصول على Token
String? fcmToken = await messaging.getToken();
print('FCM Token: $fcmToken');
```

### Android (Kotlin):
```kotlin
FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
    if (!task.isSuccessful) {
        Log.w(TAG, "Fetching FCM registration token failed", task.exception)
        return@addOnCompleteListener
    }
    val token = task.result
    Log.d(TAG, "FCM Token: $token")
}
```

### iOS (Swift):
```swift
Messaging.messaging().token { token, error in
  if let error = error {
    print("Error fetching FCM registration token: \(error)")
  } else if let token = token {
    print("FCM registration token: \(token)")
  }
}
```

---

## 📤 2. إرسال FCM Token إلى Backend

### API Endpoint:
```
POST /api/notifications/update-token
```

### Headers:
```
Authorization: Bearer {user_auth_token}
Content-Type: application/json
```

### Request Body:
```json
{
  "fcm_token": "user_fcm_token_here"
}
```

### Response (Success):
```json
{
  "success": true,
  "message": "FCM token updated successfully"
}
```

### Response (Error):
```json
{
  "success": false,
  "message": "Validation errors",
  "errors": {
    "fcm_token": ["The fcm token field is required."]
  }
}
```

---

## 🔄 3. متى يجب تحديث FCM Token؟

يجب تحديث Token في الحالات التالية:

1. **عند تسجيل الدخول (Login)**
2. **عند فتح التطبيق (App Launch)** - للتأكد من أن Token محدث
3. **عند تجديد Token** - Firebase قد يجدد Token تلقائياً

### Flutter Example:
```dart
// عند تسجيل الدخول
Future<void> updateFCMToken(String authToken) async {
  try {
    // الحصول على Token
    String? fcmToken = await FirebaseMessaging.instance.getToken();
    
    if (fcmToken != null) {
      // إرسال Token إلى Backend
      final response = await http.post(
        Uri.parse('https://your-api.com/api/notifications/update-token'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'fcm_token': fcmToken,
        }),
      );
      
      if (response.statusCode == 200) {
        print('FCM Token updated successfully');
      }
    }
  } catch (e) {
    print('Error updating FCM token: $e');
  }
}

// استمع لتجديد Token
FirebaseMessaging.instance.onTokenRefresh.listen((newToken) {
  // أعد إرسال Token الجديد إلى Backend
  updateFCMToken(userAuthToken);
});
```

---

## 📨 4. أنواع الإشعارات المتوقعة

### أ) إشعار Route/Camping جديد:
```json
{
  "notification": {
    "title": "New Route",
    "body": "Route Name - Description..."
  },
  "data": {
    "type": "new_route_camping",
    "site_id": "123",
    "site_type": "route",  // أو "camping"
    "site_name": "Route Name"
  }
}
```

### ب) إشعار قبول في رحلة:
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

---

## 🎯 5. التعامل مع الإشعارات الواردة

### Flutter - Handle Foreground Messages:
```dart
// في main.dart أو app initialization
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Got a message whilst in the foreground!');
  print('Message data: ${message.data}');
  
  if (message.notification != null) {
    print('Message also contained a notification: ${message.notification}');
    
    // عرض إشعار محلي
    _showLocalNotification(message);
    
    // التعامل مع البيانات
    _handleNotificationData(message.data);
  }
});

// Handle Background Messages (يجب أن تكون top-level function)
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print("Handling a background message: ${message.messageId}");
  print('Message data: ${message.data}');
  
  // التعامل مع البيانات
  _handleNotificationData(message.data);
}

// في main()
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  
  // تسجيل background handler
  FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  
  runApp(MyApp());
}

// Handle Notification Tap (عند الضغط على الإشعار)
FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  print('A new onMessageOpenedApp event was published!');
  _handleNotificationTap(message.data);
});
```

### التعامل مع البيانات:
```dart
void _handleNotificationData(Map<String, dynamic> data) {
  String type = data['type'] ?? '';
  
  switch (type) {
    case 'new_route_camping':
      // عرض Route/Camping جديد
      String siteId = data['site_id'] ?? '';
      String siteType = data['site_type'] ?? '';
      String siteName = data['site_name'] ?? '';
      
      // انتقل إلى صفحة Route/Camping
      Navigator.pushNamed(
        context,
        '/site-details',
        arguments: {'siteId': siteId},
      );
      break;
      
    case 'trip_accepted':
      // عرض رحلة مقبولة
      String tripId = data['trip_id'] ?? '';
      String tripName = data['trip_name'] ?? '';
      
      // انتقل إلى صفحة الرحلة
      Navigator.pushNamed(
        context,
        '/trip-details',
        arguments: {'tripId': tripId},
      );
      break;
      
    default:
      print('Unknown notification type: $type');
  }
}
```

---

## 🔔 6. عرض إشعارات محلية (Local Notifications)

### Flutter (flutter_local_notifications):
```dart
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
    FlutterLocalNotificationsPlugin();

// تهيئة Local Notifications
Future<void> initializeLocalNotifications() async {
  const AndroidInitializationSettings initializationSettingsAndroid =
      AndroidInitializationSettings('@mipmap/ic_launcher');

  const InitializationSettings initializationSettings =
      InitializationSettings(
    android: initializationSettingsAndroid,
  );

  await flutterLocalNotificationsPlugin.initialize(
    initializationSettings,
  );
}

// عرض إشعار محلي
Future<void> _showLocalNotification(RemoteMessage message) async {
  const AndroidNotificationDetails androidPlatformChannelSpecifics =
      AndroidNotificationDetails(
    'high_importance_channel',
    'High Importance Notifications',
    channelDescription: 'This channel is used for important notifications.',
    importance: Importance.high,
    priority: Priority.high,
  );

  const NotificationDetails platformChannelSpecifics =
      NotificationDetails(android: androidPlatformChannelSpecifics);

  await flutterLocalNotificationsPlugin.show(
    message.hashCode,
    message.notification?.title,
    message.notification?.body,
    platformChannelSpecifics,
    payload: jsonEncode(message.data),
  );
}
```

---

## 📋 7. Checklist للتكامل الكامل

- [ ] تثبيت Firebase SDK في التطبيق
- [ ] الحصول على FCM Token
- [ ] إرسال Token إلى Backend عند تسجيل الدخول
- [ ] إرسال Token عند فتح التطبيق
- [ ] الاستماع لتجديد Token وإعادة الإرسال
- [ ] التعامل مع الإشعارات في Foreground
- [ ] التعامل مع الإشعارات في Background
- [ ] التعامل مع الضغط على الإشعار (Notification Tap)
- [ ] التنقل إلى الصفحات المناسبة حسب نوع الإشعار
- [ ] اختبار الإشعارات في جميع الحالات

---

## 🧪 8. اختبار الإشعارات

### من Backend:
1. إنشاء Route/Camping جديد من لوحة التحكم
2. قبول حجز رحلة من API أو لوحة التحكم

### من Firebase Console:
يمكنك إرسال إشعار تجريبي من Firebase Console:
1. اذهب إلى Firebase Console → Cloud Messaging
2. اضغط "Send test message"
3. أدخل FCM Token
4. أرسل الإشعار

---

## 📝 9. أمثلة كاملة (Flutter)

### ملف: `services/notification_service.dart`
```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class NotificationService {
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  static final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();

  // تهيئة الخدمة
  static Future<void> initialize(String authToken) async {
    // تهيئة Local Notifications
    await _initializeLocalNotifications();
    
    // طلب صلاحيات الإشعارات
    NotificationSettings settings = await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );
    
    if (settings.authorizationStatus == AuthorizationStatus.authorized) {
      print('User granted permission');
      
      // الحصول على Token وإرساله
      await updateFCMToken(authToken);
      
      // الاستماع لتجديد Token
      _messaging.onTokenRefresh.listen((newToken) {
        updateFCMToken(authToken);
      });
      
      // التعامل مع الإشعارات
      _setupMessageHandlers();
    }
  }

  // تحديث FCM Token في Backend
  static Future<void> updateFCMToken(String authToken) async {
    try {
      String? fcmToken = await _messaging.getToken();
      
      if (fcmToken != null) {
        final response = await http.post(
          Uri.parse('https://your-api.com/api/notifications/update-token'),
          headers: {
            'Authorization': 'Bearer $authToken',
            'Content-Type': 'application/json',
          },
          body: jsonEncode({
            'fcm_token': fcmToken,
          }),
        );
        
        if (response.statusCode == 200) {
          print('FCM Token updated successfully');
        } else {
          print('Failed to update FCM token: ${response.body}');
        }
      }
    } catch (e) {
      print('Error updating FCM token: $e');
    }
  }

  // تهيئة Local Notifications
  static Future<void> _initializeLocalNotifications() async {
    const AndroidInitializationSettings androidSettings =
        AndroidInitializationSettings('@mipmap/ic_launcher');
    
    const InitializationSettings initSettings =
        InitializationSettings(android: androidSettings);
    
    await _localNotifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        if (response.payload != null) {
          Map<String, dynamic> data = jsonDecode(response.payload!);
          _handleNotificationTap(data);
        }
      },
    );
  }

  // إعداد معالجات الإشعارات
  static void _setupMessageHandlers() {
    // Foreground messages
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('Foreground message received');
      _showLocalNotification(message);
      _handleNotificationData(message.data);
    });

    // Background messages (يجب أن تكون top-level function)
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

    // Notification tap (عند فتح التطبيق من الإشعار)
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      print('Notification tapped');
      _handleNotificationTap(message.data);
    });
  }

  // عرض إشعار محلي
  static Future<void> _showLocalNotification(RemoteMessage message) async {
    const AndroidNotificationDetails androidDetails =
        AndroidNotificationDetails(
      'high_importance_channel',
      'High Importance Notifications',
      channelDescription: 'This channel is used for important notifications.',
      importance: Importance.high,
      priority: Priority.high,
    );

    const NotificationDetails notificationDetails =
        NotificationDetails(android: androidDetails);

    await _localNotifications.show(
      message.hashCode,
      message.notification?.title,
      message.notification?.body,
      notificationDetails,
      payload: jsonEncode(message.data),
    );
  }

  // التعامل مع بيانات الإشعار
  static void _handleNotificationData(Map<String, dynamic> data) {
    String type = data['type'] ?? '';
    print('Notification type: $type');
    
    // يمكنك إضافة منطق إضافي هنا
    // مثل تحديث البيانات في التطبيق
  }

  // التعامل مع الضغط على الإشعار
  static void _handleNotificationTap(Map<String, dynamic> data) {
    String type = data['type'] ?? '';
    
    switch (type) {
      case 'new_route_camping':
        String siteId = data['site_id'] ?? '';
        // انتقل إلى صفحة Site Details
        // Navigator.pushNamed(context, '/site-details', arguments: {'siteId': siteId});
        break;
        
      case 'trip_accepted':
        String tripId = data['trip_id'] ?? '';
        // انتقل إلى صفحة Trip Details
        // Navigator.pushNamed(context, '/trip-details', arguments: {'tripId': tripId});
        break;
    }
  }
}

// Background message handler (يجب أن تكون top-level function)
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print("Handling a background message: ${message.messageId}");
}
```

### استخدام الخدمة في `main.dart`:
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  
  // تهيئة Notification Service (بعد تسجيل الدخول)
  String authToken = await getAuthToken(); // احصل على token من storage
  if (authToken != null) {
    await NotificationService.initialize(authToken);
  }
  
  runApp(MyApp());
}
```

---

## ⚠️ ملاحظات مهمة:

1. **Android**: تأكد من إضافة `google-services.json` في `android/app/`
2. **iOS**: تأكد من إضافة `GoogleService-Info.plist` في `ios/Runner/`
3. **Permissions**: تأكد من طلب صلاحيات الإشعارات
4. **Background**: Background handler يجب أن يكون top-level function
5. **Token Refresh**: استمع لتجديد Token وأعد إرساله للـ Backend

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من Logs في Backend: `storage/logs/laravel.log`
2. تحقق من Firebase Console → Cloud Messaging → Reports
3. تأكد من أن FCM Token تم إرساله بنجاح للـ Backend

