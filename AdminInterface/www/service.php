<?php
	$change=0;
	$onlinejson = file_get_contents('https://raw.githubusercontent.com/splitti/MuPiBox/main/config/templates/mupiboxconfig.json');
	$dataonline = json_decode($onlinejson, true);
	include ('includes/header.php');

	if( $_POST['change_samba'] == "enable & start" )
		{
		$command = "sudo apt-get install samba -y && sudo wget https://raw.githubusercontent.com/splitti/MuPiBox/main/config/templates/smb.conf -O /etc/samba/smb.conf && sudo systemctl enable smbd.service && sudo systemctl start smbd.service";
		exec($command, $output, $result );
		$change=1;
		}
	else if( $_POST['change_samba'] == "stop & disable" )
		{
		$command = "sudo systemctl stop smbd.service && sudo systemctl disable smbd.service && sudo apt-get remove samba -y";
		exec($command, $output, $result );
		$change=1;
		}

	if( $_POST['change_ftp'] == "enable & start" )
		{
		$command = " sudo apt-get install proftpd -y && sudo apt-get install samba -y && sudo wget https://raw.githubusercontent.com/splitti/MuPiBox/main/config/templates/proftpd.conf -O /etc/proftpd/proftpd.conf && sudo systemctl restart proftpd";
		exec($command, $output, $result );
		$change=1;
		}
	else if( $_POST['change_ftp'] == "stop & disable" )
		{
		$command = "sudo systemctl stop proftpd.service && sudo systemctl disable proftpd.service && sudo apt-get remove proftpd -y";
		exec($command, $output, $result );
		$change=1;
		}

	if( $_POST['change_autoconnectbt'] == "enable & start" )
		{
		$command = "sudo systemctl enable mupi_autoconnect_bt; sudo systemctl start mupi_autoconnect_bt";
		exec($command, $output, $result );
		$change=1;
		}
	else if( $_POST['change_autoconnectbt'] == "stop & disable" )
		{
		$command = "sudo systemctl stop mupi_autoconnect_bt; sudo systemctl disable mupi_autoconnect_bt";
		exec($command, $output, $result );
		$change=1;
		}


	$rc = $output[count($output)-1];
	$command = "sudo service smbd status | grep running";
	exec($command, $smboutput, $smbresult );
	if( $smboutput[0] )
		{
		$command="/usr/bin/hostname -I | awk '{print $1}'";
		$IP=exec($command);
		$samba_state = "started&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;&nbsp;UNC-Path: \\\\".$IP."\\mupibox\\&nbsp;&nbsp;]";
		$change_samba = "stop & disable";
		}
	else
		{
		$samba_state = "disabled";
		$change_samba = "enable & start";
		}

	$command = "sudo service proftpd status | grep running";
	exec($command, $ftpoutput, $ftpresult );
	if( $ftpoutput[0] )
		{
		$ftp_state = "started";
		$change_ftp = "stop & disable";
		}
	else
		{
		$ftp_state = "disabled";
		$change_ftp = "enable & start";
		}

	$command = "sudo service mupi_autoconnect_bt status | grep running";
	exec($command, $btacoutput, $btacresult );
	if( $btacoutput[0] )
		{
		$btac_state = "started";
		$change_btac = "stop & disable";
		}
	else
		{
		$btac_state = "disabled";
		$change_btac = "enable & start";
		}

?>

                <form class="appnitro"  method="post" action="service.php" id="form">
                                        <div class="description">
                        <h2>MupiBox settings</h2>
                        <p>De/Activate some helpfull services...</p>
                </div>
                        <ul >
                                        
								<li class="li_1"><h2>Samba</h2>
								<p>
								<?php 
								echo "Samba Status: <b>".$samba_state."</b>";
								?>
								</p>
								<input id="saveForm" class="button_text" type="submit" name="change_samba" value="<?php print $change_samba; ?>" /></li>
								<li class="li_1"><h2>FTP-Server</h2>
								<p>
								<?php 
								echo "FTP-Server Status: <b>".$ftp_state."</b>";
								?>
								</p>
								<input id="saveForm" class="button_text" type="submit" name="change_btac" value="<?php print $change_btac; ?>" /></li>
								<li class="li_1"><h2>Bluetooth Autoconnect Helper (Just if automatic reconnect won't work)</h2>
								<p>
								<?php 
								echo "BT Autoconnect Status: <b>".$btac_state."</b>";
								?>
								</p>
								<input id="saveForm" class="button_text" type="submit" name="change_btac" value="<?php print $change_btac; ?>" /></li>

                        </ul>
                </form>
<?php
	include ('includes/footer.php');
?>