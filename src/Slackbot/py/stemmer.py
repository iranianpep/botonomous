from __future__ import print_function
import sys, json, traceback
from nltk.stem import PorterStemmer
from nltk.tokenize import word_tokenize

stemmer = PorterStemmer()

try:
    #TODO use argparse instead this check
    #get the arguments passed
    argList = sys.argv

    if (len(sys.argv) > 1):
        data = json.loads(argList[1])
        words = word_tokenize(data[0])

        sentence = " ".join(PorterStemmer().stem_word(word) for word in words);

        # remove the new line using end=''
        print(sentence, end='')
    else:
        print('', end='')
except:
    traceback.print_exc()
