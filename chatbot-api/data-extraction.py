import sys
import subprocess
subprocess.check_call([sys.executable, '-m', 'pip', 'install', 'langchain'])
reqs = subprocess.check_output([sys.executable, '-m', 'pip', 'freeze'])
installed_packages = [r.decode().split('==')[0] for r in reqs.split()]
print(installed_packages)
from langchain.text_splitter import CharacterTextSplitter

urls = [
    'https://acclaim.agency/blog/how-artificial-intelligence-revolutionizes-wordpress-development'
     ]

from langchain.document_loaders import UnstructuredURLLoader
loaders = UnstructuredURLLoader(urls=['https://acclaim.agency/blog/how-artificial-intelligence-revolutionizes-wordpress-development'])
data = loaders.load()
text_splitter = CharacterTextSplitter(separator='\n',
                                      chunk_size=1000,
                                      chunk_overlap=200)
docs = text_splitter.split_documents(data)


