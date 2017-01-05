from __future__ import print_function
import sys, json, traceback
from nltk.stem.porter import *
from nltk.tokenize import word_tokenize

try:
    #get the arguments passed
    argList = sys.argv

    if (len(sys.argv) > 1):
        data = json.loads(argList[1])
        words = word_tokenize(data[0])

        sentence = " ".join(PorterStemmer().stem(word) for word in words);

        # remove the new line using end=''
        print(sentence, end='')
    else:
        print('', end='')
except Exception:
    traceback.print_exc()
