# 🚗 AutoSmart - سوق قطع غيار السيارات

<div align="center">

![AutoSmart Logo](https://via.placeholder.com/200x60?text=AutoSmart)

**منصة التجارة الإلكترونية المتخصصة في قطع غيار السيارات**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3_RTL-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)

</div>

---

## 📋 نظرة عامة

**AutoSmart** هو سوق إلكتروني متكامل يربط بين:
- 🛒 **أصحاب السيارات** - للبحث والشراء بسهولة
- 🏪 **تجار قطع الغيار** - لعرض وبيع منتجاتهم
- 🔧 **ورش الصيانة** - لتقديم خدماتهم وإدارة أعمالهم

---

## ✨ المميزات الرئيسية

### 🛍️ للمشترين
- ✅ بحث ذكي عن القطع بالاسم أو رقم القطعة أو VIN
- ✅ مقارنة الأسعار بين المتاجر
- ✅ ضمان الجودة وحماية المشتري
- ✅ تتبع الطلبات والشحنات
- ✅ برنامج ولاء ونقاط مكافآت
- ✅ تقسيط المشتريات

### 🏬 للبائعين
- ✅ لوحة تحكم متكاملة
- ✅ إدارة المنتجات والمخزون
- ✅ نظام نقاط بيع (POS)
- ✅ تحليلات وتقارير مفصلة
- ✅ أدوات تسويق متقدمة
- ✅ إدارة فروع متعددة

### 🔧 لورش الصيانة
- ✅ نظام حجز مواعيد
- ✅ إدارة الخدمات والأسعار
- ✅ ربط مع موردي القطع
- ✅ فواتير إلكترونية
- ✅ تقييمات العملاء

---

## 🚀 التقنيات المستخدمة

| التقنية | الاستخدام |
|---------|----------|
| **Laravel 12** | إطار العمل الأساسي |
| **Livewire 3** | الواجهات التفاعلية |
| **Bootstrap 5 RTL** | تصميم الواجهات |
| **SQLite/MySQL** | قاعدة البيانات |
| **Sanctum** | مصادقة API |

---

## 📦 التثبيت

### المتطلبات

```
PHP >= 8.2
Composer >= 2.0
SQLite أو MySQL 8.0+
```

### الخطوات

```bash
# 1. استنساخ المشروع
git clone https://github.com/autosmart/autosmart.git
cd AutoSmart

# 2. تثبيت التبعيات
composer install

# 3. إعداد البيئة
cp .env.example .env
php artisan key:generate

# 4. إعداد قاعدة البيانات
touch database/database.sqlite
php artisan migrate --seed

# 5. ربط التخزين
php artisan storage:link

# 6. تشغيل الخادم
php artisan serve
```

### الوصول للتطبيق

```
🌐 الموقع: http://localhost:8000
👤 Admin: admin@autosmart.com / password
🏪 Seller: seller@autosmart.com / password
🔧 Workshop: workshop@autosmart.com / password
```

---

## 📂 هيكل المشروع

```
AutoSmart/
├── app/
│   ├── Http/Controllers/     # 60+ متحكم
│   ├── Models/               # 80+ نموذج
│   ├── Services/             # خدمات الأعمال
│   └── Livewire/             # مكونات تفاعلية
├── database/
│   ├── migrations/           # 50+ ملف ترحيل
│   └── seeders/              # بيانات تجريبية
├── resources/views/          # 200+ قالب
├── routes/                   # 394 route
└── docs/                     # التوثيق
    ├── FEASIBILITY_STUDY.md  # دراسة الجدوى
    └── PROJECT_DOCUMENTATION.md # توثيق المشروع
```

---

## 📊 إحصائيات المشروع

| المكون | العدد |
|--------|-------|
| Controllers | 60+ |
| Models | 80+ |
| Views | 200+ |
| Migrations | 50+ |
| Routes | 394 |
| Features | 117 |

---

## 🎯 الميزات حسب المراحل

### المرحلة 1: الأساسيات
- المستخدمين والمصادقة
- المتاجر والمنتجات
- الطلبات والسلة
- التصنيفات والبحث

### المرحلة 2: الإضافات
- الكوبونات والعروض
- المحادثات الفورية
- العناوين والشحن
- الضمان والمراجعات

### المرحلة 3: المتقدمة
- نظام سياراتي
- المدفوعات المتكاملة
- إدارة المخزون
- برنامج الولاء
- ورش الصيانة

### المرحلة 4: الذكية
- الأسئلة والأجوبة Q&A
- البحث بـ VIN
- نظام المزادات
- التتبع المباشر
- حسابات B2B
- بطاقات الهدايا
- الاشتراكات

### المرحلة 5: الاحترافية
- التسعير الديناميكي (AI)
- توقع نفاد المخزون
- تحليل المشاعر
- المنتجات المجمعة
- التسويق بالعمولة
- نظام الفروع
- نقاط البيع POS
- تكامل المحاسبة
- الواقع المعزز AR

---

## 📚 التوثيق

| الوثيقة | الوصف |
|---------|-------|
| [دراسة الجدوى](docs/FEASIBILITY_STUDY.md) | تحليل السوق والمالية |
| [توثيق المشروع](docs/PROJECT_DOCUMENTATION.md) | الخصائص التقنية والوظيفية |

---

## 🔐 الأمان

- ✅ HTTPS/TLS Encryption
- ✅ CSRF Protection
- ✅ Two-Factor Authentication
- ✅ SQL Injection Prevention
- ✅ XSS Protection
- ✅ Rate Limiting
- ✅ Audit Logging

---

## 🌐 API

```bash
# مثال: جلب المنتجات
curl -X GET "http://localhost:8000/api/products" \
     -H "Authorization: Bearer {token}" \
     -H "Accept: application/json"
```

للمزيد راجع [توثيق API](docs/PROJECT_DOCUMENTATION.md#واجهات-البرمجة-api)

---

## 🤝 المساهمة

1. Fork المشروع
2. إنشاء فرع للميزة (`git checkout -b feature/amazing`)
3. Commit التغييرات (`git commit -m 'Add amazing feature'`)
4. Push للفرع (`git push origin feature/amazing`)
5. فتح Pull Request

---

## 📞 التواصل

- 📧 Email: info@autosmart.com
- 🌐 Website: www.autosmart.com
- 📱 Phone: 920XXXXXX

---

## 📄 الترخيص

هذا المشروع مُرخص بموجب [MIT License](LICENSE).

---

<div align="center">

**صُنع بـ ❤️ في المملكة العربية السعودية**

© 2026 AutoSmart. جميع الحقوق محفوظة.

</div>
