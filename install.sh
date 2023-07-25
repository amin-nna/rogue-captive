#!/bin/sh -e

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root." 1>&2
   exit 1
fi

echo "Installing dependencies..."
apt update
apt -y install macchanger hostapd dnsmasq apache2 php

echo "Configuring components..."
cp -f hostapd.conf /etc/hostapd/
cp -f dnsmasq.conf /etc/


# Step 2: Configure hostapd
echo "Step 2: Configuring hostapd"
sudo tee /etc/hostapd/hostapd.conf > /dev/null <<EOL
# 2.4GHz setup wifi 80211 b,g,n
interface=wlan0
driver=nl80211
ssid=Pi Wifi
hw_mode=g
channel=8
wmm_enabled=0
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=2
wpa_passphrase=raspizerow
wpa_key_mgmt=WPA-PSK
wpa_pairwise=CCMP TKIP
rsn_pairwise=CCMP
country_code=US
ieee80211n=1
ieee80211d=1
EOL

# Update the defaults file
echo "Updating /etc/default/hostapd"
sudo sed -i 's/#DAEMON_CONF=""/DAEMON_CONF="\/etc\/hostapd\/hostapd.conf"/' /etc/default/hostapd

# Step 3: Configure dnsmasq
echo "Step 3: Configuring dnsmasq"
sudo tee /etc/dnsmasq.conf > /dev/null <<EOL
# AutoHotspot config
interface=wlan0
bind-dynamic
server=8.8.8.8
domain-needed
bogus-priv
dhcp-range=192.168.50.150,192.168.50.200,12h
EOL

# Step 4: Set up ip forwarding
echo "Step 4: Setting up IP forwarding"
sudo sed -i 's/#net.ipv4.ip_forward=1/net.ipv4.ip_forward=1/' /etc/sysctl.conf

# Step 5: Disable wifi network in dhcpcd
echo "Step 5: Disabling wifi network in dhcpcd"
echo "nohook wpa_supplicant" | sudo tee -a /etc/dhcpcd.conf

# Step 6: Create the autohotspot service file
echo "Step 6: Creating autohotspot service file"
sudo tee /etc/systemd/system/autohotspot.service > /dev/null <<EOL
[Unit]
Description=Automatically generates an internet Hotspot when a valid SSID is not in range
After=multi-user.target

[Service]
Type=oneshot
RemainAfterExit=yes
ExecStart=/usr/bin/autohotspotN

[Install]
WantedBy=multi-user.target
EOL

# Step 7: Create the autohotspot script
echo "Step 7: Creating autohotspot script"
sudo tee /usr/bin/autohotspotN > /dev/null <<EOL
# The content of the autohotspot script goes here (Copy the script provided in the instructions)
EOL

# Step 8: Make the autohotspot script executable
echo "Step 8: Making the autohotspot script executable"
sudo chmod +x /usr/bin/autohotspotN

# Step 9: Enable the autohotspot service
echo "Step 9: Enabling the autohotspot service"
sudo systemctl enable autohotspot.service


cp -rf html /var/www/
chown -R www-data:www-data /var/www/html
chown root:www-data /var/www/html/.htaccess
cp -f rc.local /etc/
cp -f override.conf /etc/apache2/conf-available/
a2enconf override
a2enmod rewrite


echo "Rogue captive portal installed. Reboot to execute."
exit 0



echo "Installation completed successfully!"
echo "Please reboot your Raspberry Pi to start using the hotspot."

