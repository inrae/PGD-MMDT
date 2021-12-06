#!/bin/bash

# Creation du logigramme au format HTML, version 1

# Generation des fichiers map, png
gvgen -dh3 | dot logigramme_origine.gv -Tpng -Gsize=13,19\! -Gdpi=125 -o logigramme_origine.png -Tcmapx -o logigramme_origine.map

gvgen -dh3 | dot logigramme_stockage.gv -Tpng -Gsize=11,17\! -Gdpi=130 -o logigramme_stockage.png -Tcmapx -o logigramme_stockage.map

gvgen -dh3 | dot logigramme_archivage.gv -Tpng -Gsize=9,11\! -Gdpi=130 -o logigramme_archivage.png -Tcmapx -o logigramme_archivage.map

gvgen -dh3 | dot logigramme_valorisation.gv -Tpng -Gsize=12,18\! -Gdpi=130 -o logigramme_valorisation.png -Tcmapx -o logigramme_valorisation.map

gvgen -dh3 | dot logigramme_droits.gv -Tpng -Gsize=11,17\! -Gdpi=130 -o logigramme_droits.png -Tcmapx -o logigramme_droits.map

# Generation des fichiers HTML
