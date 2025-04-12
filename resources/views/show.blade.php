<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل المنتج</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .product-img {
            max-height: 400px;
            width: 100%;
            object-fit: contain;
        }
        .product-details {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" class="card-img-top product-img" alt="{{ $product->name }}">
                    @else
                        <div class="card-body text-center">
                            <i class="fas fa-image fa-5x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد صورة لهذا المنتج</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details h-100">
                    <h2 class="mb-4">{{ $product->name }}</h2>
                    
                    <div class="detail-item">
                        <h5><i class="fas fa-tag me-2 text-primary"></i> الفئة</h5>
                        <p class="ps-4">{{ $product->category->name ?? 'بدون فئة' }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <h5><i class="fas fa-money-bill-wave me-2 text-success"></i> السعر</h5>
                        <p class="ps-4">{{ $product->price }} ج.م</p>
                    </div>
                    
                    <div class="detail-item">
                        <h5><i class="fas fa-boxes me-2 text-info"></i> الكمية المتاحة</h5>
                        <p class="ps-4">{{ $product->quantity }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <h5><i class="fas fa-info-circle me-2 text-warning"></i> الحالة</h5>
                        <p class="ps-4">
                            <span class="badge {{ $product->stock_status == 'in_stock' ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->stock_status == 'in_stock' ? 'متوفر' : 'غير متوفر' }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-2"></i> تعديل المنتج
                        </a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                <i class="fas fa-trash-alt me-2"></i> حذف المنتج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>