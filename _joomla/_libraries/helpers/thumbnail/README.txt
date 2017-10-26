# 26.10.2017
# CUSTOMIZAÇÕES NA CLASSE "EasyPhpThumbnail" ORIGINAL

:: PROBLEMA
As imagens do tipo PNG com transparência, ficam com o background preto após serem cortadas
através do método "imagecrop".

:: SOLUÇÃO
Adicionar os dois métodos "imagealphablending" e "imagesavealpha"
após a chamada do método "imagecreatetruecolor" no método "imagecrop".

-> LINHA# 1402
imagealphablending($this->newimage, false);
imagesavealpha($this->newimage, true);
-> LINHA# 1409
imagealphablending($this->im, false);
imagesavealpha($this->im, true);
