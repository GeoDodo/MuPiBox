#!/bin/dash
# Autostart run script for kiosk mode, based on @AYapejian: https://github.com/MichaIng/DietPi/issues/1737#issue-318697621
# - Please see /root/.chromium-browser.init (and /etc/chromium.d/custom_flags) for additional falgs.
# Command line switches: https://peter.sh/experiments/chromium-command-line-switches/
# --test-type gets rid of some of the Chromium warnings that you may or may not care about in kiosk on a LAN
# --pull-to-refresh=1
# --ash-host-window-bounds="400,300"
# Resolution to use for kiosk mode, should ideally match current system resolution

CONFIG="/etc/mupibox/mupiboxconfig.json"
RES_X=$(/usr/bin/jq -r .chromium.resX ${CONFIG})
RES_Y=$(/usr/bin/jq -r .chromium.resY ${CONFIG})
SONOS_NETWORK="/home/dietpi/.mupibox/Sonos-Kids-Controller-master/server/config/network.json"

CHROMIUM_OPTS="--use-gl=egl --kiosk --test-type --window-size=${RES_X:-1280},${RES_Y:-720} --start-fullscreen --start-maximized --window-position=0,0"
# If you want tablet mode, uncomment the next line.
#CHROMIUM_OPTS+=' --force-tablet-mode --tablet-ui'
# Home page

URL="http://$(/usr/bin/jq -r .mupibox.host ${CONFIG}):8200"

START_VOLUME=$(/usr/bin/jq -r .mupibox.startVolume ${CONFIG})
/usr/bin/amixer sset ${AUDIO_DEVICE} ${START_VOLUME}%

currentIP=$(hostname -I)
/usr/bin/cat <<< $(/usr/bin/jq --arg v "${currentIP}" '.[].ip = $v' ${SONOS_NETWORK}) >  ${SONOS_NETWORK}
currentHost=$(hostname)
/usr/bin/cat <<< $(/usr/bin/jq --arg v "${currentHost}" '.[].host = $v' ${SONOS_NETWORK}) >  ${SONOS_NETWORK}

# RPi or Debian Chromium package
FP_CHROMIUM=$(command -v chromium-browser)
[ "$FP_CHROMIUM" ] || FP_CHROMIUM=$(command -v chromium)
exec nice -n 19 xinit "$FP_CHROMIUM" $CHROMIUM_OPTS --homepage "${URL:-http://MuPiBox:8200}" -- -nocursor tty2
