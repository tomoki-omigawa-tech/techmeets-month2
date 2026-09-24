<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>S3画像アップロード</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 40px auto;">

    <h1>S3画像アップロード</h1>

    @if (session('url'))
        <div style="margin-bottom: 20px;">
            <p><strong>アップロード成功！</strong></p>
            @if (session('conversion'))
                <p style="color: #059669;">{{ session('conversion') }}</p>
            @endif
            <img src="{{ session('url') }}" alt="uploaded image" style="max-width: 100%; border-radius: 8px;">
            <p style="font-size: 12px; word-break: break-all; color: #666;">{{ session('url') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('s3upload.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" style="margin-top: 10px; padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px;">
            アップロード
        </button>
    </form>

</body>
</html>
