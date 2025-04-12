<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المنتج</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .edit-form {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="edit-form">
                    <div class="form-header">
                        <h2 class="mb-0">تعديل المنتج: {{ $product->name }}</h2>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.products.update', $product->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المنتج</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">الفئة</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">اختر الفئة...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">السعر</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $product->price }}" required>
                                    <span class="input-group-text">ج.م</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">الكمية</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $product->quantity }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock_status" class="form-label">حالة المخزون</label>
                            <select class="form-select" id="stock_status" name="stock_status" required>
                                <option value="in_stock" {{ $product->stock_status == 'in_stock' ? 'selected' : '' }}>متوفر</option>
                                <option value="out_of_stock" {{ $product->stock_status == 'out_of_stock' ? 'selected' : '' }}>غير متوفر</option>
                            </select>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-2"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-2"></i> إلغاء
                            </a>
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info">
                                <i class="fas fa-eye me-2"></i> عرض المنتج
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
