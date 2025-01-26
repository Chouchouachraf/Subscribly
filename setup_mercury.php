<?php
// Configuration paths
$mercuryPath = 'C:/xampp/MercuryMail';
$mercuryIni = $mercuryPath . '/MERCURY.INI';

// Backup original configuration
if (file_exists($mercuryIni)) {
    copy($mercuryIni, $mercuryIni . '.bak');
}

// Basic Mercury configuration
$config = "
[Mercury]
LocalDomain=localhost
PostmasterAddr=postmaster@localhost
SmtpPort=25
RootDir=C:\\xampp\\MercuryMail
TempDir=C:\\xampp\\MercuryMail\\TEMP
MaxRetries=4
RetryGap=30
MaxHops=30
MaxRecips=100
PurgeTime=7
MaxMsgSize=10000
LogLevel=2
DNTimeout=15
WarnTimeout=4
MaxConnects=5
MaxErrors=10
TCPPort=25
AllowRelay=2

[POP3]
TCPPort=110
MaxConnects=5
MaxErrors=10

[Aliases]
postmaster=postmaster@localhost

[Queue]
QueueDir=C:\\xampp\\MercuryMail\\QUEUE
";

// Write new configuration
file_put_contents($mercuryIni, $config);

echo "Mercury configuration has been updated.\n";
echo "Please restart Mercury in XAMPP Control Panel for changes to take effect.\n";
?>
