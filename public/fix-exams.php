<?php
/**
 * Exam System Fix Tool
 * Direct database constraint fixes
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$db = $app->make('db');

$response = [
    'success' => false,
    'message' => 'Unknown action',
    'data' => null
];

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'display';

    if ($action === 'fix') {
        // تطبيق إصلاح القيود
        $db->statement('SET FOREIGN_KEY_CHECKS=0');

        // حذف المفاتيح القديمة
        try {
            $db->statement('ALTER TABLE exam_answers DROP FOREIGN KEY exam_answers_student_id_foreign');
        } catch (\Exception $e) {}

        try {
            $db->statement('ALTER TABLE exam_answers DROP FOREIGN KEY exam_answers_exam_id_foreign');
        } catch (\Exception $e) {}

        try {
            $db->statement('ALTER TABLE exam_answers DROP FOREIGN KEY exam_answers_question_id_foreign');
        } catch (\Exception $e) {}

        // إضافة المفاتيح الجديدة مع RESTRICT
        $db->statement('
            ALTER TABLE exam_answers
            ADD CONSTRAINT exam_answers_student_id_foreign
            FOREIGN KEY (student_id) REFERENCES students(id) 
            ON DELETE RESTRICT ON UPDATE CASCADE
        ');

        $db->statement('
            ALTER TABLE exam_answers
            ADD CONSTRAINT exam_answers_exam_id_foreign
            FOREIGN KEY (exam_id) REFERENCES exam_names(id) 
            ON DELETE RESTRICT ON UPDATE CASCADE
        ');

        $db->statement('
            ALTER TABLE exam_answers
            ADD CONSTRAINT exam_answers_question_id_foreign
            FOREIGN KEY (question_id) REFERENCES exam_questions(id) 
            ON DELETE RESTRICT ON UPDATE CASCADE
        ');

        $db->statement('SET FOREIGN_KEY_CHECKS=1');

        $response = [
            'success' => true,
            'message' => '✅ تم إصلاح القيود الخارجية بنجاح!'
        ];

    } elseif ($action === 'check') {
        // التحقق من القيود
        $constraints = $db->select("
            SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_NAME IN ('exam_answers', 'exam_questions', 'exam_results')
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        $response = [
            'success' => true,
            'message' => 'تم الحصول على معلومات القيود',
            'data' => $constraints
        ];

    } elseif ($action === 'clear') {
        // حذف الإجابات القديمة
        $deleted = $db->table('exam_answers')->delete();

        $response = [
            'success' => true,
            'message' => 'تم حذف ' . $deleted . ' إجابة قديمة',
            'data' => ['deleted_count' => $deleted]
        ];
    }

} catch (\Exception $e) {
    $response = [
        'success' => false,
        'message' => 'خطأ: ' . $e->getMessage(),
        'error_detail' => $e->getFile() . ':' . $e->getLine()
    ];
}

// إذا كانت طلب AJAX، ارجع JSON
if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصلاح نظام الامتحانات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .status.loading {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .button-group {
            display: grid;
            gap: 10px;
            margin: 30px 0;
        }
        
        .button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .button-primary {
            background: #667eea;
            color: white;
        }
        
        .button-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .button-secondary {
            background: #f59e0b;
            color: white;
        }
        
        .button-secondary:hover {
            background: #d97706;
            transform: translateY(-2px);
        }
        
        .button-danger {
            background: #ef4444;
            color: white;
        }
        
        .button-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        
        .button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
        }
        
        .info-box h3 {
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .info-box ul {
            margin-left: 20px;
        }
        
        .info-box li {
            margin: 5px 0;
        }
        
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 13px;
        }
        
        .constraints-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        
        .constraints-table th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: right;
        }
        
        .constraints-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .constraints-table tr:hover {
            background: #f9f9f9;
        }
        
        .progress {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        
        .spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إصلاح نظام الامتحانات</h1>
        <p class="subtitle">أداة تصحيح قيود قاعدة البيانات</p>
        
        <div id="result"></div>
        
        <div class="info-box">
            <h3>ما هي المشكلة؟</h3>
            <ul>
                <li>الإجابات تُحفظ ثم تُحذف تلقائياً</li>
                <li>القيود الخارجية مضبوطة على CASCADE DELETE</li>
                <li>يجب تغييرها إلى RESTRICT</li>
            </ul>
        </div>
        
        <div class="button-group">
            <button class="button button-primary" onclick="fixDatabase()">
                ✅ الخطوة 1: إصلاح القيود
            </button>
            
            <button class="button button-secondary" onclick="checkConstraints()">
                🔍 الخطوة 2: التحقق من الإصلاح
            </button>
            
            <button class="button button-danger" onclick="clearAnswers()">
                🗑️ الخطوة 3: حذف الإجابات القديمة
            </button>
        </div>
        
        <div id="constraints-result"></div>
    </div>

    <script>
        function showLoading(message = 'جاري المعالجة...') {
            document.getElementById('result').innerHTML = 
                '<div class="status loading">' + message + '</div>';
        }

        function showSuccess(message, data = null) {
            let html = '<div class="status success">' + message + '</div>';
            if (data) {
                html += '<div style="background: #f0f0f0; padding: 15px; margin-top: 10px; border-radius: 4px; overflow-x: auto;">';
                html += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                html += '</div>';
            }
            document.getElementById('result').innerHTML = html;
        }

        function showError(message) {
            document.getElementById('result').innerHTML = 
                '<div class="status error">❌ ' + message + '</div>';
        }

        function fixDatabase() {
            if (!confirm('هل تريد إصلاح القيود الخارجية؟')) return;
            
            showLoading('جاري إصلاح القيود...');
            
            fetch('?action=fix&ajax=1', {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                } else {
                    showError(data.message);
                }
            })
            .catch(e => showError('خطأ في الاتصال: ' + e.message));
        }

        function checkConstraints() {
            showLoading('جاري التحقق من القيود...');
            
            fetch('?action=check&ajax=1')
            .then(r => r.json())
            .then(data => {
                if (data.success && data.data) {
                    let html = '<h3 style="color: #667eea; margin-top: 20px;">القيود الموجودة:</h3>';
                    html += '<table class="constraints-table">';
                    html += '<tr><th>الجدول</th><th>العمود</th><th>يشير إلى</th><th>الاسم</th></tr>';
                    
                    data.data.forEach(c => {
                        html += '<tr>';
                        html += '<td>' + c.TABLE_NAME + '</td>';
                        html += '<td>' + c.COLUMN_NAME + '</td>';
                        html += '<td>' + c.REFERENCED_TABLE_NAME + '(' + c.REFERENCED_COLUMN_NAME + ')</td>';
                        html += '<td><code>' + c.CONSTRAINT_NAME + '</code></td>';
                        html += '</tr>';
                    });
                    
                    html += '</table>';
                    document.getElementById('constraints-result').innerHTML = html;
                    
                    showSuccess('✅ تم الحصول على معلومات القيود بنجاح!');
                } else {
                    showError(data.message);
                }
            })
            .catch(e => showError('خطأ: ' + e.message));
        }

        function clearAnswers() {
            if (!confirm('⚠️ هل أنت متأكد تماماً؟ سيتم حذف جميع الإجابات!\n\nهذا لا يمكن التراجع عنه!')) return;
            
            showLoading('جاري الحذف...');
            
            fetch('?action=clear&ajax=1', {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccess('🗑️ ' + data.message, data.data);
                } else {
                    showError(data.message);
                }
            })
            .catch(e => showError('خطأ: ' + e.message));
        }
    </script>
</body>
</html>
