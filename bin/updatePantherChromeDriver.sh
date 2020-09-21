#!/usr/bin/env bash
# ensure that symfony/panther's chromeDriver matches installed chromium version
# this needs chromium and symfony/panther (in vendor folder) to be installed

cd /var/www/html/vendor/symfony/panther/chromedriver-bin

chromiumVersion=$(chromium --product-version);
echo "Found chromiumVersion ${chromiumVersion}";
chromiumVersion="_$( cut -d '.' -f 1 <<< "$chromiumVersion" )";
chromeDriver=$(curl -s https://chromedriver.storage.googleapis.com/LATEST_RELEASE${chromiumVersion});
echo "Downloading ChromeDriver version ${chromeDriver} from https://chromedriver.storage.googleapis.com/LATEST_RELEASE${chromiumVersion} ..."

declare -a binaries=("chromedriver_linux64" "chromedriver_mac64" "chromedriver_win32")
for name in "${binaries[@]}"
do
   curl -s https://chromedriver.storage.googleapis.com/${chromeDriver}/${name}.zip -O
   unzip -q -o ${name}.zip
   rm ${name}.zip
   if [[ -f "chromedriver" ]]; then
      mv chromedriver ${name}
   fi
done

curl -s https://chromedriver.storage.googleapis.com/${chromeDriver}/notes.txt -O
echo "Done."

