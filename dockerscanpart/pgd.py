# -*- coding: utf8 -*-
import os
import re
import json
from pprint import pprint
from pprint import pformat

### fonctions ###

#charge json et retourne objet python
def get_json(filename):
	with open(filename, 'r') as f:
		datas = json.load(f)
		return datas

#transforme un dico clé/set en clé/liste
def set_to_list_dic(mydicset):
	mydiclist = dict()
	for key,value in mydicset.items():
		mydiclist[key] = sorted(value)
	return mydiclist

#charge le dico des options actives et retourne dico clé/set
def load_active_dic(masterdic):
	activedic = dict()
	for key,value in masterdic['chkbxdico'].items():
		activedic[key] = set(value)
	for key,value in masterdic['listdico'].items():
		activedic[key] = set(value)
	return activedic

#crée un dico vide clé/set sur la base d'un dico modéle
def init_dic(modeldic):
	maindic = dict()
	for key,value in modeldic.items():
		maindic[key] = set()
	return maindic

#fusionne dico issu du scan avec celui des options actives
def merge_dic(activedic, maindic):
	mergeddic = dict()
	for key, value in activedic.items():
		if key in maindic:
			mergeddic[key] = maindic[key].union(set(activedic[key]))
		else:
			mergeddic[key] = set(activedic[key])
	return mergeddic

#supprime les éléments vides d'un dico clé/set
def clean_dic(dico):
	for key, value in dico.items():
		dico[key].discard('')

#parcours arborescence et ajoute les données des json dans dico unique
def scan_dir(dir,dico):
	listedirpgd = os.listdir(dir)
	if '#snapshot' in listedirpgd: listedirpgd.remove('#snapshot')
	for name in listedirpgd:
		path = os.path.join(dir, name)
		if os.path.isfile(path):
			if p.match(name):
				mydata = get_json(path)
				for key,value in mydata.items():
					#print(type(value))
					if isinstance(value,list):
						if key in dico:
							dico[key].update(set(mydata[key]))
		else:
			scan_dir(path,dico)

#ré-organise les données au format du fichier master (i.e. chkbxdico et listdico) et retourne le dictionnaire
def format_dico(mydico,master):
	formated = dict()
	for key in master:
		formated[key] = {}
		for subkey in master[key]:
			formated[key][subkey] = list(sorted(mydico[subkey]))
	return formated

#ecrit fichier options.js
def dico_to_js(formated,filename):
	regsearch = re.compile('"([^:"]+)":', re.MULTILINE)
	regquote = re.compile('("[^,]+)\"([^,]+")')
	fo = open(filename, 'w')
	with open('options.js', "w") as f:
		for key in formated:
			fo.write("var "+key+" =\n")
			mystr = pformat(formated[key], indent=1, width=250)
			modstr = mystr.replace("'",'"')
			final = re.sub(regsearch,r"\1:",modstr)
			final = re.sub(regquote,r"\1'\2",final)
			fo.write(final+";\n")
	f.close()

### Prog ###
p = re.compile('PGD.*\.json')
master_json = 'Master_PGD.json'
outputfile = '../js/options.js'

masterdic = get_json(master_json)
activedic = load_active_dic(masterdic)
maindic = init_dic(activedic)

scan_dir("/pgd_data/", maindic)

alldata = merge_dic(activedic,maindic)
clean_dic(alldata)

formated = format_dico(alldata,masterdic)
dico_to_js(formated,outputfile)
