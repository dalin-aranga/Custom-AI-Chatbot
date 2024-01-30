from bs4 import BeautifulSoup
from urllib.request import Request, urlopen
import re

site = "http://www.example.com/"
hdr = {'User-Agent': 'Mozilla/5.0'}
req = Request(site, headers=hdr)
page = urlopen(req)
soup = BeautifulSoup(page, 'html.parser')
links = soup.find_all('a', href=True)

def single_page():
    multi_data = []
    plain_text = soup.get_text()
    cleaned_text = re.sub('\s+', ' ', plain_text).strip()
    key=site
    value=cleaned_text
    pair = {key: value}
    multi_data.append(pair)
    return multi_data
    
    
    
def multi_page():
    i=0
    for link in links:
        if "page=" in link['href']:
            i=i+1   
    multi_data = []
    
    for j in range(1, i):
        base_url = site+"?page="+str(j)
        req = Request(base_url, headers=hdr)
        page = urlopen(req)
        soup = BeautifulSoup(page, 'html.parser')
        plain_text = soup.get_text()
        cleaned_text = re.sub('\s+', ' ', plain_text).strip()
        key=base_url
        value=cleaned_text
        pair = {key: value}
        multi_data.append(pair)
    return multi_data
                
page_count=0
for link in links:
    
    if "page=" in link['href']:
        page_count=page_count+1
        

if page_count>0:
    
    print(multi_page())
    print("called Multi")
else:
    print(single_page())
