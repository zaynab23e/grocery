<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - إدارة المنتجات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
            margin: 2px;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .img-thumbnail {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">لوحة تحكم إدارة المنتجات</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- استيراد الفئات -->
    <div class="card mb-4">
        <div class="card-header">استيراد الفئات من Excel</div>
        <div class="card-body">
            <form action="{{ route('admin.import.categories') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <input type="file" name="excel_file" class="form-control" required accept=".xlsx,.xls">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fas fa-upload me-2"></i> استيراد الفئات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- استيراد المنتجات -->
    <div class="card mb-4">
        <div class="card-header">استيراد المنتجات من Excel</div>
        <div class="card-body">
            <form action="{{ route('admin.import.products') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <input type="file" name="excel_file" class="form-control" required accept=".xlsx,.xls">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fas fa-upload me-2"></i> استيراد المنتجات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- عرض المنتجات -->
    <div class="card">
        <div class="card-header">قائمة المنتجات</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>اسم المنتج</th>
                        <th>الفئة</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="width: 150px; height: 150px;">
                                @endif
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'بدون فئة' }}</td>
                            <td>
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <span class="text-decoration-line-through text-danger">{{ $product->price }} ج.م</span><br>
                                    <span class="text-success fw-bold">{{ $product->discount_price }} ج.م</span>
                                @else
                                    <span>{{ $product->price }} ج.م</span>
                                @endif
                            </td>
                            <td>{{ $product->quantity }}</td>
                            <td>
                                <span class="badge {{ $product->stock_status == 'in_stock' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock_status == 'in_stock' ? 'متوفر' : 'غير متوفر' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-sm" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateImageModal{{ $product->id }}" title="تغيير الصورة">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal لتحديث الصورة -->
                                <div class="modal fade" id="updateImageModal{{ $product->id }}" tabindex="-1" aria-labelledby="updateImageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="updateImageModalLabel">تحديث صورة المنتج</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.products.update-image', $product->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="product_image" class="form-label">اختر صورة جديدة</label>
                                                        <input class="form-control" type="file" id="product_image" name="product_image" required accept="image/*">
                                                    </div>
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i> سيتم حذف الصورة الحالية واستبدالها بالصورة الجديدة
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ الصورة</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- الترقيم -->
            <div class="d-flex justify-content-center mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
