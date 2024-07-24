<!DOCTYPE html>
<html>

<head>
    <title>Nadiku</title>
</head>

<body>
    <h3>{{ $list['classification'] }}</h3>

    <h4>Dari : {{ $list['fromName'] }}</h4>
    <h4>Untuk : {{ $list['allTo'] }}</h4>
    <h4>Perihal : {{ $list['perihal'] }}</h4>

    Isi Disposisi :<br>
    {!! nl2br(e($list['disposisi'])) !!}<br>

    Tenggat Waktu : {{ $list['due_date'] }}<br><br>

    Link Surat : {{ $list['link'] }}<br><br>

    <i>Pesan ini disampaikan oleh Admin BPS Kab. Acceh Utara</i>
</body>

</html>
