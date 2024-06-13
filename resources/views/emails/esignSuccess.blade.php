<!DOCTYPE html>
<html>
<head>
    <title>Masdipo</title>
</head>
<body>
    <h3>{{ $pesan['nomor_surat'] }} : ESIGN SUCCESS</h3>
    
    <h4>Dari : {{ $pesan['dari'] }}</h4>
    <h4>Untuk : {{ $pesan['tertuju'] }}</h4>
    <h4>Tanggal Surat : {{ $pesan['tanggal_surat'] }}</h4>

    <p>Link : {{ $pesan['link'] }}</p>
    
    <br><br>
    <i>Pesan ini disampaikan oleh Admin Masdipo</i>
</body>
</html>