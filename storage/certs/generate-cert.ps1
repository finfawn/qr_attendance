$cert = New-SelfSignedCertificate `
    -Subject "CN=QR Attendance Local Dev" `
    -DnsName "localhost","127.0.0.1","192.168.100.9" `
    -KeyAlgorithm RSA `
    -KeyLength 2048 `
    -NotBefore (Get-Date) `
    -NotAfter (Get-Date).AddYears(1) `
    -CertStoreLocation "Cert:\LocalMachine\My" `
    -FriendlyName "QR Attendance Dev Certificate" `
    -HashAlgorithm SHA256 `
    -KeyUsage DigitalSignature, KeyEncipherment, DataEncipherment `
    -TextExtension @("2.5.29.37={text}1.3.6.1.5.5.7.3.1")

$pwd = ConvertTo-SecureString -String "qr-attendance-dev" -Force -AsPlainText
$certPath = "Cert:\LocalMachine\My\$($cert.Thumbprint)"

# Export .pfx file
$pfxPath = ".\localhost.pfx"
Export-PfxCertificate -Cert $certPath -FilePath $pfxPath -Password $pwd

# Export .cer file (public key only)
$cerPath = ".\localhost.cer"
Export-Certificate -Cert $certPath -FilePath $cerPath

# Create key and crt files for Laravel
$pfxBytes = Get-Content -Path $pfxPath -AsByteStream
$pfxContent = [System.Security.Cryptography.X509Certificates.X509Certificate2]::new($pfxBytes, $pwd)

# Export private key
$rsaPrivateKey = [System.Security.Cryptography.X509Certificates.RSACertificateExtensions]::GetRSAPrivateKey($pfxContent)
$keyBytes = $rsaPrivateKey.Key.Export([System.Security.Cryptography.CngKeyBlobFormat]::Pkcs8PrivateBlob)
[System.IO.File]::WriteAllBytes(".\localhost.key", $keyBytes)

# Export certificate
[System.IO.File]::WriteAllBytes(".\localhost.crt", $pfxContent.Export([System.Security.Cryptography.X509Certificates.X509ContentType]::Cert))
