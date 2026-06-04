<?php
    class TwentySixTheme
    {
        public function info()
        {
            return [
                'title'       => 'Twenty Six',
                'logo'        => 'assets/logo.jpg'
            ];
        }

        public function fields()
        {
            return [
                [
                    'name'  => 'enable_bg_image',
                    'label' => 'Background Image',
                    'type'  => 'select',
                    'options' => [
                        'enabled'  => 'Enable',
                        'disabled' => 'Disable',
                    ],
                    'value' => 'disabled',
                    'required' => false,
                    'multiple' => false,
                ],
                [
                    'name'  => 'background_image',
                    'label' => 'Background Image',
                    'required' => false,
                    'type'  => 'image',
                ],
                [
                    'name'  => 'watermark_text',
                    'label' => 'Footer Branding Text',
                    'type'  => 'text',
                    'value' => 'Powered by BillPax',
                    'required' => false,
                    'placeholder' => 'e.g. Powered by BillPax'
                ],
                [
                    'name'  => 'seo_title',
                    'label' => 'SEO Title',
                    'type'  => 'text',
                    'required' => false,
                    'placeholder' => 'Enter SEO title (max 60 characters)',
                ],
                [
                    'name'  => 'seo_description',
                    'label' => 'SEO Description',
                    'type'  => 'textarea',
                    'required' => false,
                    'placeholder' => 'Enter SEO description (max 160 characters)',
                ],
                [
                    'name'  => 'seo_keywords',
                    'label' => 'SEO Keywords',
                    'type'  => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. billing, invoicing, payments',
                ],
                [
                    'name'  => 'analytics_code',
                    'label' => 'Analytics & Tracking Code',
                    'type'  => 'textarea',
                    'required' => false,
                    'placeholder' => 'Paste Google Analytics, GTM, or other tracking code',
                ],
                [
                    'name'  => 'primary_color',
                    'label' => 'Primary Color',
                    'type'  => 'color',
                    'value' => '#5f38f9',
                    'required' => false,
                    'placeholder' => '',
                ],
                [
                    'name'  => 'text_color',
                    'label' => 'Text Color',
                    'type'  => 'color',
                    'value' => '#FFFFFF',
                    'required' => false,
                    'placeholder' => '',
                ],
            ];
        }

        public function supported_languages()
        {
            return [
                'en' => 'English',
                'bn' => 'বাংলা',
                'hi' => 'हिन्दी',
                'ur' => 'اردو',
                'ar' => 'العربية',
            ];
        }

        public function lang_text()
        {
            return [
                'payment_link' => [
                    'en' => 'Payment Link',
                    'bn' => 'পেমেন্ট লিঙ্ক',        // Bengali
                    'hi' => 'भुगतान लिंक',         // Hindi
                    'ur' => 'ادائیگی کا لنک',       // Urdu
                    'ar' => 'رابط الدفع',           // Arabic
                ],

                'select_language' => [
                    'en' => 'Select your native language',
                    'bn' => 'আপনার মাতৃভাষা নির্বাচন করুন',   // Bengali
                    'hi' => 'अपनी मूल भाषा चुनें',            // Hindi
                    'ur' => 'اپنی مادری زبان منتخب کریں',     // Urdu
                    'ar' => 'اختر لغتك الأم',                // Arabic
                ],

                'language' => [
                    'en' => 'Language',
                    'bn' => 'ভাষা',                     // Bengali
                    'hi' => 'भाषा',                      // Hindi
                    'ur' => 'زبان',                       // Urdu
                    'ar' => 'اللغة',                      // Arabic
                ],

                'select_a_language' => [
                    'en' => 'Select a language',
                    'bn' => 'একটি ভাষা নির্বাচন করুন',       // Bengali
                    'hi' => 'एक भाषा चुनें',                 // Hindi
                    'ur' => 'ایک زبان منتخب کریں',           // Urdu
                    'ar' => 'اختر لغة',                     // Arabic
                ],

                'close' => [
                    'en' => 'Close',
                    'bn' => 'বন্ধ করুন',                   // Bengali
                    'hi' => 'बंद करें',                    // Hindi
                    'ur' => 'بند کریں',                     // Urdu
                    'ar' => 'إغلاق',                        // Arabic
                ],
                'full_name' => [
                    'en' => 'Full Name',
                    'bn' => 'পূর্ণ নাম',             // Correct Bengali
                    'hi' => 'पूरा नाम',             // Correct Hindi
                    'ur' => 'پورا نام',             // Correct Urdu
                    'ar' => 'الاسم الكامل',          // Correct Arabic
                ],

                'email_address' => [
                    'en' => 'Email Address',
                    'bn' => 'ইমেইল ঠিকানা',         // Bengali
                    'hi' => 'ईमेल पता',              // Hindi
                    'ur' => 'ای میل پتہ',            // Urdu
                    'ar' => 'عنوان البريد الإلكتروني', // Arabic
                ],

                'mobile_number' => [
                    'en' => 'Mobile Number',
                    'bn' => 'মোবাইল নম্বর',          // Bengali
                    'hi' => 'मोबाइल नंबर',           // Hindi
                    'ur' => 'موبائل نمبر',           // Urdu
                    'ar' => 'رقم الجوال',            // Arabic
                ],

                'amount' => [
                    'en' => 'Amount',
                    'bn' => 'পরিমাণ',               // Bengali
                    'hi' => 'राशि',                  // Hindi
                    'ur' => 'رقم',                   // Urdu
                    'ar' => 'المبلغ',                 // Arabic
                ],

                'pay_now' => [
                    'en' => 'Pay Now',
                    'bn' => 'এখনই পরিশোধ করুন',     // Bengali
                    'hi' => 'अभी भुगतान करें',        // Hindi
                    'ur' => 'ابھی ادائیگی کریں',      // Urdu
                    'ar' => 'ادفع الآن',              // Arabic
                ],

                'invoice' => [
                    'en' => 'Invoice',
                    'bn' => 'চালান',
                    'hi' => 'चालान',
                    'ur' => 'انوائس',
                    'ar' => 'فاتورة',
                ],

                'invoice_date' => [
                    'en' => 'Invoice Date',
                    'bn' => 'চালানের তারিখ',
                    'hi' => 'चालान दिनांक',
                    'ur' => 'تاریخ انوائس',
                    'ar' => 'تاريخ الفاتورة',
                ],

                'due_date' => [
                    'en' => 'Due Date',
                    'bn' => 'সময়সীমা',
                    'hi' => 'अंतिम तिथि',
                    'ur' => 'ادائیگی کی آخری تاریخ',
                    'ar' => 'تاريخ الاستحقاق',
                ],

                'payment_method' => [
                    'en' => 'Payment Method',
                    'bn' => 'পরিশোধের পদ্ধতি',
                    'hi' => 'भुगतान विधि',
                    'ur' => 'ادائیگی کا طریقہ',
                    'ar' => 'طريقة الدفع',
                ],

                'bill_from' => [
                    'en' => 'Bill From',
                    'bn' => 'বিল পাঠানো হয়েছে',
                    'hi' => 'बिल प्रेषक',
                    'ur' => 'بل بھیجا گیا',
                    'ar' => 'فاتورة من',
                ],

                'email' => [
                    'en' => 'Email',
                    'bn' => 'ইমেইল',
                    'hi' => 'ईमेल',
                    'ur' => 'ای میل',
                    'ar' => 'البريد الإلكتروني',
                ],

                'phone' => [
                    'en' => 'Phone',
                    'bn' => 'ফোন',
                    'hi' => 'फोन',
                    'ur' => 'فون',
                    'ar' => 'الهاتف',
                ],

                'bill_to' => [
                    'en' => 'Bill To',
                    'bn' => 'বিলের গ্রাহক',
                    'hi' => 'बिल प्राप्तकर्ता',
                    'ur' => 'بل وصول کنندہ',
                    'ar' => 'فاتورة إلى',
                ],

                'description' => [
                    'en' => 'Description',
                    'bn' => 'বিবরণ',
                    'hi' => 'विवरण',
                    'ur' => 'تفصیل',
                    'ar' => 'الوصف',
                ],

                'qty' => [
                    'en' => 'Qty',
                    'bn' => 'পরিমাণ',
                    'hi' => 'मात्रा',
                    'ur' => 'تعداد',
                    'ar' => 'الكمية',
                ],

                'unit_price' => [
                    'en' => 'Unit Price',
                    'bn' => 'একক মূল্য',
                    'hi' => 'इकाई मूल्य',
                    'ur' => 'فی یونٹ قیمت',
                    'ar' => 'سعر الوحدة',
                ],

                'note' => [
                    'en' => 'Notes',
                    'bn' => 'নোট',
                    'hi' => 'टिप्पणियाँ',
                    'ur' => 'نوٹس',
                    'ar' => 'ملاحظات',
                ],

                'subtotal' => [
                    'en' => 'Subtotal',
                    'bn' => 'উপ-মোট',
                    'hi' => 'उप-योग',
                    'ur' => 'ذیلی مجموعہ',
                    'ar' => 'المجموع الفرعي',
                ],

                'shipping' => [
                    'en' => 'Shipping',
                    'bn' => 'শিপিং',
                    'hi' => 'शिपिंग',
                    'ur' => 'شپنگ',
                    'ar' => 'الشحن',
                ],

                'tax' => [
                    'en' => 'Tax (VAT)',
                    'bn' => 'ট্যাক্স (ভ্যাট)',
                    'hi' => 'कर (वैट)',
                    'ur' => 'ٹیکس (وی اے ٹی)',
                    'ar' => 'الضريبة (ضريبة القيمة المضافة)',
                ],

                'discount' => [
                    'en' => 'Discount',
                    'bn' => 'ছাড়',
                    'hi' => 'छूट',
                    'ur' => 'رعایت',
                    'ar' => 'الخصم',
                ],

                'total' => [
                    'en' => 'Total',
                    'bn' => 'মোট',
                    'hi' => 'कुल',
                    'ur' => 'کل',
                    'ar' => 'الإجمالي',
                ],

                'total_due' => [
                    'en' => 'Total Due',
                    'bn' => 'মোট প্রদেয়',
                    'hi' => 'कुल देय',
                    'ur' => 'کل واجب الادا',
                    'ar' => 'الإجمالي المستحق',
                ],

                'no_signature' => [
                    'en' => 'This is an electronically generated invoice. No signature required.',
                    'bn' => 'এটি একটি ইলেকট্রনিকভাবে তৈরি চালান। কোনো স্বাক্ষরের প্রয়োজন নেই।',
                    'hi' => 'यह एक इलेक्ट्रॉनिक रूप से जनरेट किया गया चालान है। कोई हस्ताक्षर आवश्यक नहीं।',
                    'ur' => 'یہ ایک الیکٹرانک طور پر تیار کردہ انوائس ہے۔ دستخط کی ضرورت نہیں۔',
                    'ar' => 'هذه فاتورة تم إنشاؤها إلكترونيًا. لا حاجة للتوقيع.',
                ],

                'print_invoice' => [
                    'en' => 'Print Invoice',
                    'bn' => 'চালান প্রিন্ট করুন',
                    'hi' => 'चालान प्रिंट करें',
                    'ur' => 'انوائس پرنٹ کریں',
                    'ar' => 'طباعة الفاتورة',
                ],

                'badge_paid' => [
                    'en' => 'Paid',
                    'bn' => 'পরিশোধিত',
                    'hi' => 'भुगतान किया गया',
                    'ur' => 'ادا شدہ',
                    'ar' => 'مدفوع',
                ],

                'badge_unpaid' => [
                    'en' => 'Unpaid',
                    'bn' => 'অপরিশোধিত',
                    'hi' => 'अवैतनिक',
                    'ur' => 'غیر ادا شدہ',
                    'ar' => 'غير مدفوع',
                ],

                'badge_refunded' => [
                    'en' => 'Refunded',
                    'bn' => 'ফিরতি হয়েছে',
                    'hi' => 'वापस किया गया',
                    'ur' => 'رقم واپس کیا گیا',
                    'ar' => 'تم استرداده',
                ],

                'badge_canceled' => [
                    'en' => 'Canceled',
                    'bn' => 'বাতিল',
                    'hi' => 'रद्द किया गया',
                    'ur' => 'منسوخ شدہ',
                    'ar' => 'ملغاة',
                ],
                'checkout' => [
                    'en' => 'Checkout',
                    'bn' => 'চেকআউট',
                    'hi' => 'चेकआउट',
                    'ur' => 'چیک آؤٹ',
                    'ar' => 'الدفع',
                ],

                'complete_payment' => [
                    'en' => 'Complete your payment',
                    'bn' => 'পেমেন্ট সম্পন্ন করুন',
                    'hi' => 'अपना भुगतान पूरा करें',
                    'ur' => 'اپنی ادائیگی مکمل کریں',
                    'ar' => 'أكمل الدفع',
                ],

                'choose_payment_method' => [
                    'en' => 'Payment method',
                    'bn' => 'পেমেন্ট পদ্ধতি',
                    'hi' => 'भुगतान विधि',
                    'ur' => 'ادائیگی کا طریقہ',
                    'ar' => 'طريقة الدفع',
                ],

                'order_summary' => [
                    'en' => 'Order summary',
                    'bn' => 'অর্ডার সারাংশ',
                    'hi' => 'ऑर्डर सारांश',
                    'ur' => 'آرڈر کا خلاصہ',
                    'ar' => 'ملخص الطلب',
                ],

                'total_due' => [
                    'en' => 'Total due',
                    'bn' => 'মোট পরিশোধ',
                    'hi' => 'कुल देय',
                    'ur' => 'کل واجب الادا',
                    'ar' => 'المبلغ المستحق',
                ],

                'transaction_ref' => [
                    'en' => 'Transaction ref',
                    'bn' => 'ট্রানজ্যাকশন রেফ',
                    'hi' => 'लेन-देन संदर्भ',
                    'ur' => 'لین دین حوالہ',
                    'ar' => 'مرجع المعاملة',
                ],

                'processing_fee' => [
                    'en' => 'Processing fee',
                    'bn' => 'প্রসেসিং ফি',
                    'hi' => 'प्रसंस्करण शुल्क',
                    'ur' => 'پروسیسنگ فیس',
                    'ar' => 'رسوم المعالجة',
                ],

                'discount' => [
                    'en' => 'Discount',
                    'bn' => 'ছাড়',
                    'hi' => 'छूट',
                    'ur' => 'رعایت',
                    'ar' => 'خصم',
                ],

                'secured_checkout' => [
                    'en' => 'Secure checkout',
                    'bn' => 'নিরাপদ চেকআউট',
                    'hi' => 'सुरक्षित चेकआउट',
                    'ur' => 'محفوظ چیک آؤٹ',
                    'ar' => 'دفع آمن',
                ],

                'how_to_pay' => [
                    'en' => 'How to pay',
                    'bn' => 'কীভাবে পেমেন্ট করবেন',
                    'hi' => 'कैसे भुगतान करें',
                    'ur' => 'کیسے ادائیگی کریں',
                    'ar' => 'كيفية الدفع',
                ],

                'switch_payment_method' => [
                    'en' => 'Change payment method',
                    'bn' => 'পেমেন্ট পদ্ধতি পরিবর্তন',
                    'hi' => 'भुगतान विधि बदलें',
                    'ur' => 'ادائیگی کا طریقہ تبدیل کریں',
                    'ar' => 'تغيير طريقة الدفع',
                ],

                'switch_payment_hint' => [
                    'en' => 'Pick another wallet or bank if needed',
                    'bn' => 'প্রয়োজনে অন্য বিকাশ, নগদ বা কার্ড বেছে নিন',
                    'hi' => 'जरूरत हो तो दूसरा वॉलेट चुनें',
                    'ur' => 'ضرورت ہو تو دوسرا طریقہ منتخب کریں',
                    'ar' => 'اختر محفظة أو بنكًا آخر عند الحاجة',
                ],

                'pay_with_amount' => [
                    'en' => 'Pay {amount} {currency}',
                    'bn' => '{amount} {currency} পরিশোধ করুন',
                    'hi' => '{amount} {currency} भुगतान करें',
                    'ur' => '{amount} {currency} ادا کریں',
                    'ar' => 'ادفع {amount} {currency}',
                ],

                'secured_by_brand' => [
                    'en' => 'Secured by {brand}',
                    'bn' => '{brand} দ্বারা সুরক্ষিত',
                    'hi' => '{brand} द्वारा सुरक्षित',
                    'ur' => '{brand} کے ذریعے محفوظ',
                    'ar' => 'مؤمّن بواسطة {brand}',
                ],

                'terms_notice' => [
                    'en' => 'By clicking the Pay button you agree to our {terms} which is limited to facilitating your payment to {brand}.',
                    'bn' => 'পে বাটনে ক্লিক করে আপনি আমাদের {terms}-এ সম্মত হন, যা শুধু {brand}-এ আপনার পেমেন্ট সহজতার জন্য।',
                    'hi' => 'पे बटन पर क्लिक करके आप हमारी {terms} से सहमत होते हैं।',
                    'ur' => 'پے بٹن پر کلک کر کے آپ ہماری {terms} سے اتفاق کرتے ہیں۔',
                    'ar' => 'بالنقر على زر الدفع فإنك توافق على {terms}.',
                ],

                'terms_of_service' => [
                    'en' => 'Terms of Service',
                    'bn' => 'সেবার শর্তাবলী',
                    'hi' => 'सेवा की शर्तें',
                    'ur' => 'سروس کی شرائط',
                    'ar' => 'شروط الخدمة',
                ],

                'select_payment_option' => [
                    'en' => 'Select payment option',
                    'bn' => 'পেমেন্ট অপশন বেছে নিন',
                    'hi' => 'भुगतान विकल्प चुनें',
                    'ur' => 'ادائیگی کا اختیار منتخب کریں',
                    'ar' => 'اختر خيار الدفع',
                ],

                'contact_support' => [
                    'en' => 'Contact support',
                    'bn' => 'সাপোর্টে যোগাযোগ',
                    'hi' => 'सहायता से संपर्क करें',
                    'ur' => 'سپورٹ سے رابطہ',
                    'ar' => 'اتصل بالدعم',
                ],

                'copy' => [
                    'en' => 'Copy',
                    'bn' => 'কপি',
                    'hi' => 'कॉपी',
                    'ur' => 'کاپی',
                    'ar' => 'نسخ',
                ],

                'got_it' => [
                    'en' => 'Got it!',
                    'bn' => 'বুঝেছি!',
                    'hi' => 'समझ गया!',
                    'ur' => 'سمجھ گیا!',
                    'ar' => 'حسنًا!',
                ],

                'bank_transfer' => [
                    'en' => 'Bank Transfer',
                    'bn' => 'ব্যাংক ট্রান্সফার',
                    'hi' => 'बैंक ट्रांसफर',
                    'ur' => 'بینک ٹرانسفر',
                    'ar' => 'تحويل بنكي',
                ],

                'mobile_banking' => [
                    'en' => 'Mobile Banking',
                    'bn' => 'মোবাইল ব্যাংকিং',
                    'hi' => 'मोबाइल बैंकिंग',
                    'ur' => 'موبائل بینکنگ',
                    'ar' => 'الخدمات المصرفية عبر الهاتف المحمول',
                ],

                'net_banking' => [
                    'en' => 'Net Banking',
                    'bn' => 'নেট ব্যাংকিং',
                    'hi' => 'नेट बैंकिंग',
                    'ur' => 'نیٹ بینکنگ',
                    'ar' => 'الخدمات المصرفية عبر الإنترنت',
                ],

                'global' => [
                    'en' => 'Global',
                    'bn' => 'গ্লোবাল',
                    'hi' => 'ग्लोबल',
                    'ur' => 'عالمی',
                    'ar' => 'عالمي',
                ],

                'contact_fb_page' => [
                    'en' => 'Contact via Fb Page',
                    'bn' => 'ফেসবুক পেজের মাধ্যমে যোগাযোগ করুন',
                    'hi' => 'फेसबुक पेज के माध्यम से संपर्क करें',
                    'ur' => 'فیس بک پیج کے ذریعے رابطہ کریں',
                    'ar' => 'تواصل عبر صفحة فيسبوك',
                ],

                'contact_messenger' => [
                    'en' => 'Contact via Messenger',
                    'bn' => 'মেসেঞ্জারের মাধ্যমে যোগাযোগ করুন',
                    'hi' => 'मैसेंजर के माध्यम से संपर्क करें',
                    'ur' => 'میسنجر کے ذریعے رابطہ کریں',
                    'ar' => 'تواصل عبر ماسنجر',
                ],

                'contact_website' => [
                    'en' => 'Visit Website',
                    'bn' => 'ওয়েবসাইটে যান',
                    'hi' => 'वेबसाइट पर जाएं',
                    'ur' => 'ویب سائٹ پر جائیں',
                    'ar' => 'زيارة الموقع',
                ],

                'contact_telegram' => [
                    'en' => 'Contact via Telegram',
                    'bn' => 'টেলিগ্রামের মাধ্যমে যোগাযোগ করুন',
                    'hi' => 'टेलीग्राम के माध्यम से संपर्क करें',
                    'ur' => 'ٹیلیگرام کے ذریعے رابطہ کریں',
                    'ar' => 'تواصل عبر تيليجرام',
                ],

                'contact_whatsapp' => [
                    'en' => 'Contact via Whatsapp',
                    'bn' => 'হোয়াটসঅ্যাপের মাধ্যমে যোগাযোগ করুন',
                    'hi' => 'व्हाट्सएप के माध्यम से संपर्क करें',
                    'ur' => 'واٹس ایپ کے ذریعے رابطہ کریں',
                    'ar' => 'تواصل عبر واتساب',
                ],

                'contact_phone' => [
                    'en' => 'Contact via Phone',
                    'bn' => 'ফোনের মাধ্যমে যোগাযোগ করুন',
                    'hi' => 'फोन के माध्यम से संपर्क करें',
                    'ur' => 'فون کے ذریعے رابطہ کریں',
                    'ar' => 'تواصل عبر الهاتف',
                ],

                'contact_email' => [
                    'en' => 'Contact via Email',
                    'bn' => 'ইমেলের মাধ্যমে যোগাযোগ করুন',
                    'hi' => 'ईमेल के माध्यम से संपर्क करें',
                    'ur' => 'ای میل کے ذریعے رابطہ کریں',
                    'ar' => 'تواصل عبر البريد الإلكتروني',
                ],

                'currency' => [
                    'en' => 'Currency',
                    'bn' => 'মুদ্রা',
                    'hi' => 'मुद्रा',
                    'ur' => 'کرنسی',
                    'ar' => 'العملة',
                ],

                'download_receipt' => [
                    'en' => 'Download Receipt',
                    'bn' => 'রসিদ ডাউনলোড করুন',
                    'hi' => 'रसीद डाउनलोड करें',
                    'ur' => 'رسید ڈاؤن لوڈ کریں',
                    'ar' => 'تحميل الإيصال',
                ],

                'status' => [
                    'en' => 'Status',
                    'bn' => 'স্ট্যাটাস',
                    'hi' => 'स्थिति',
                    'ur' => 'حالت',
                    'ar' => 'الحالة',
                ],

                'net_local_amount' => [
                    'en' => 'Net Local Amount',
                    'bn' => 'নেট লোকাল পরিমাণ',
                    'hi' => 'शुद्ध स्थानीय राशि',
                    'ur' => 'خالص مقامی رقم',
                    'ar' => 'صافي المبلغ المحلي',
                ],

                'net_amount' => [
                    'en' => 'Net Amount',
                    'bn' => 'নেট পরিমাণ',
                    'hi' => 'शुद्ध राशि',
                    'ur' => 'خالص رقم',
                    'ar' => 'صافي المبلغ',
                ],

                'processing_fee' => [
                    'en' => 'Processing Fee',
                    'bn' => 'প্রসেসিং ফি',
                    'hi' => 'प्रोसेसिंग शुल्क',
                    'ur' => 'پروسیسنگ فیس',
                    'ar' => 'رسوم المعالجة',
                ],

                'go_to_site' => [
                    'en' => 'Go to Site',
                    'bn' => 'সাইটে যান',
                    'hi' => 'साइट पर जाएं',
                    'ur' => 'سائٹ پر جائیں',
                    'ar' => 'الانتقال إلى الموقع',
                ],

                'change_status_completed' => [
                    'en' => 'Your transaction has been completed successfully.',
                    'bn' => 'আপনার লেনদেন সফলভাবে সম্পন্ন হয়েছে।',
                    'hi' => 'आपका लेनदेन सफलतापूर्वक पूरा हो गया है।',
                    'ur' => 'آپ کی ٹرانزیکشن کامیابی کے ساتھ مکمل ہو گئی ہے۔',
                    'ar' => 'تم إكمال معاملتك بنجاح.',
                ],

                'change_status_pending' => [
                    'en' => 'Your payment is pending. It will be verified manually.',
                    'bn' => 'আপনার পেমেন্ট মুলতুবি রয়েছে। এটি ম্যানুয়ালি যাচাই করা হবে।',
                    'hi' => 'आपका भुगतान लंबित है। इसे मैन्युअल रूप से सत्यापित किया जाएगा।',
                    'ur' => 'آپ کی ادائیگی زیر التواء ہے۔ اس کی دستی طور پر تصدیق کی جائے گی۔',
                    'ar' => 'دفعتك قيد الانتظار. سيتم التحقق منها يدويًا.',
                ],

                'change_status_refunded' => [
                    'en' => 'Your payment has been refunded to your account.',
                    'bn' => 'আপনার পেমেন্ট আপনার অ্যাকাউন্টে ফেরত দেওয়া হয়েছে।',
                    'hi' => 'आपका भुगतान आपके खाते में वापस कर दिया गया है।',
                    'ur' => 'آپ کی ادائیگی آپ کے اکاؤنٹ میں واپس کر دی گئی ہے۔',
                    'ar' => 'تم رد المبلغ إلى حسابك.',
                ],

                'change_status_cancled' => [
                    'en' => 'Your payment was canceled. No charge was made.',
                    'bn' => 'আপনার পেমেন্ট বাতিল করা হয়েছে। কোনো চার্জ কাটা হয়নি।',
                    'hi' => 'आपका भुगतान रद्द कर दिया गया था। कोई शुल्क नहीं लिया गया।',
                    'ur' => 'آپ کی ادائیگی منسوخ کر دی گئی تھی۔ کوئی چارج نہیں لیا گیا۔',
                    'ar' => 'تم إلغاء دفعتك. لم يتم خصم أي مبلغ.',
                ],
                'payment_successful' => [
                    'en' => 'Payment Successful!',
                    'bn' => 'পেমেন্ট সফল হয়েছে!',
                    'hi' => 'भुगतान सफल हुआ!',
                    'ur' => 'ادائیگی کامیاب ہو گئی!',
                    'ar' => 'تمت عملية الدفع بنجاح!',
                ],

                'payment_pending' => [
                    'en' => 'Payment Pending',
                    'bn' => 'পেমেন্ট প্রক্রিয়াধীন',
                    'hi' => 'भुगतान लंबित है',
                    'ur' => 'ادائیگی زیر التواء ہے',
                    'ar' => 'الدفع قيد الانتظار',
                ],

                'payment_refunded' => [
                    'en' => 'Payment Refunded',
                    'bn' => 'পেমেন্ট ফেরত দেওয়া হয়েছে',
                    'hi' => 'भुगतान वापस कर दिया गया है',
                    'ur' => 'ادائیگی واپس کر دی گئی ہے',
                    'ar' => 'تم رد المبلغ',
                ],

                'payment_canceled' => [
                    'en' => 'Payment Canceled',
                    'bn' => 'পেমেন্ট বাতিল করা হয়েছে',
                    'hi' => 'भुगतान रद्द कर दिया गया है',
                    'ur' => 'ادائیگی منسوخ کر دی گئی ہے',
                    'ar' => 'تم إلغاء الدفع',
                ],
                'product_not_active' => [
                    'en' => 'Product Not Active',
                    'bn' => 'পণ্যটি সক্রিয় নয়',
                    'hi' => 'उत्पाद सक्रिय नहीं है',
                    'ur' => 'مصنوعہ فعال نہیں ہے',
                    'ar' => 'المنتج غير نشط',
                ],

                'product_not_active_text' => [
                    'en' => 'This product is currently inactive, so the payment link is not available.',
                    'bn' => 'এই পণ্যটি বর্তমানে নিষ্ক্রিয় রয়েছে, তাই পেমেন্ট লিংকটি উপলব্ধ নয়।',
                    'hi' => 'यह उत्पाद वर्तमान में निष्क्रिय है, इसलिए भुगतान लिंक उपलब्ध नहीं है।',
                    'ur' => 'یہ پروڈکٹ اس وقت غیر فعال ہے، اس لیے ادائیگی کا لنک دستیاب نہیں ہے۔',
                    'ar' => 'هذا المنتج غير نشط حاليًا، لذلك رابط الدفع غير متوفر.',
                ],

                'something_wrong' => [
                    'en' => 'Something Went Wrong!',
                    'bn' => 'কিছু একটা সমস্যা হয়েছে!',
                    'hi' => 'कुछ गलत हो गया!',
                    'ur' => 'کچھ غلط ہو گیا!',
                    'ar' => 'حدث خطأ ما!',
                ],

                'support_contact_text' => [
                    'en' => 'For further assistance, please contact our support team.',
                    'bn' => 'আরও সহায়তার জন্য, আমাদের সাপোর্ট টিমের সাথে যোগাযোগ করুন।',
                    'hi' => 'अधिक सहायता के लिए, कृपया हमारी सहायता टीम से संपर्क करें।',
                    'ur' => 'مزید مدد کے لیے، براہ کرم ہماری سپورٹ ٹیم سے رابطہ کریں۔',
                    'ar' => 'للمزيد من المساعدة، يرجى التواصل مع فريق الدعم.',
                ],

                'copied_successfully' => [
                    'en' => 'Copied Successfully',
                    'bn' => 'সফলভাবে কপি হয়েছে',
                    'hi' => 'सफलतापूर्वक कॉपी हो गया',
                    'ur' => 'کامیابی سے کاپی ہو گیا',
                    'ar' => 'تم النسخ بنجاح',
                ],

                'copy_content_copied' => [
                    'en' => 'The content has been copied to your clipboard.',
                    'bn' => 'বিষয়বস্তু আপনার ক্লিপবোর্ডে কপি করা হয়েছে।',
                    'hi' => 'सामग्री आपके क्लिपबोर्ड पर कॉपी कर दी गई है।',
                    'ur' => 'مواد آپ کے کلپ بورڈ پر کاپی کر دیا گیا ہے۔',
                    'ar' => 'تم نسخ المحتوى إلى الحافظة.',
                ],

                'copy_failed' => [
                    'en' => 'Copy Failed!',
                    'bn' => 'কপি ব্যর্থ হয়েছে!',
                    'hi' => 'कॉपी विफल हो गई!',
                    'ur' => 'کاپی ناکام ہو گئی!',
                    'ar' => 'فشل النسخ!',
                ],

                'copy_failed_text' => [
                    'en' => 'Unable to copy. Please try manually.',
                    'bn' => 'কপি করা সম্ভব হয়নি। অনুগ্রহ করে ম্যানুয়ালি চেষ্টা করুন।',
                    'hi' => 'कॉपी नहीं हो सका। कृपया मैन्युअल रूप से प्रयास करें।',
                    'ur' => 'کاپی نہیں ہو سکی۔ براہ کرم دستی طور پر کوشش کریں۔',
                    'ar' => 'تعذّر النسخ. يرجى المحاولة يدويًا.',
                ],

                'copy_no_content' => [
                    'en' => 'No content provided to copy.',
                    'bn' => 'কপি করার জন্য কোনো বিষয়বস্তু নেই।',
                    'hi' => 'कॉपी करने के लिए कोई सामग्री नहीं मिली।',
                    'ur' => 'کاپی کرنے کے لیے کوئی مواد فراہم نہیں کیا گیا۔',
                    'ar' => 'لا يوجد محتوى للنسخ.',
                ],









            ];
        }

        public function renderCheckout($data = [])
        {
            if($data['transaction']['status'] == "initiated"){
                if(isset($_GET['gateway'])){
                    include(__DIR__.'/gateway.php');
                }else{
                    include(__DIR__.'/checkout.php');
                }
            }else{
                include(__DIR__.'/checkout-status.php');
            }
        }

        public function renderInvoice($data = [])
        {
            include(__DIR__.'/invoice.php');
        }

        public function renderPaymentLink($data = [])
        {
            include(__DIR__.'/payment-link.php');
        }

        public function renderPaymentLinkDefault($data = [])
        {
            include(__DIR__.'/payment-link-default.php');
        }
    }
