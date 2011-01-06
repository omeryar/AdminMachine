String.prototype.phpimageeditor_add_editlink_endswith = function(str) 
{
	return (this.match(str+"$")==str);
}

function phpimageeditor_add_editlink(pathToEditor, pathToPlugin, hostPath, editImageText, language)
{
	var mediamanagerForm = document.getElementById("mediamanager-form");
	if (mediamanagerForm != null)
	{
		var trs = mediamanagerForm.getElementsByTagName("tr");
		var modeDetailed = (trs.length > 0);
		
		if (modeDetailed)
		{
			var isTableHeader = true;
			
			for(var i=0;i<trs.length;i++)
			{
				if (isTableHeader)
				{
					var th = document.createElement("th");
					th.appendChild(document.createTextNode(editImageText));
					trs[i].appendChild(th);
				}
				else 
				{
					var td = document.createElement("td");
					
					var imageSrcDetailed = "";
					var links = trs[i].getElementsByTagName("a");
					var foundDetailedImage = false;

					for(var c=0;c<links.length;c++)
					{
						if (links[c].className == 'img-preview')
						{
							imageSrcDetailed = phpimageeditor_urlencode(links[c].href.replace(hostPath,'../../../')); 
							
							if (phpimageeditor_file_is_image(imageSrcDetailed))
							{
								if (trs[i].innerHTML.indexOf('folderup_16.png') == -1 && trs[i].innerHTML.indexOf('folder_sm.png') == -1)
								{
									td.innerHTML = '<div><a style="background-position: 5px 0; background-image: url('+pathToPlugin+'images/edit.gif); background-repeat: no-repeat; padding-bottom: 4px; padding-left: 22px;" href="'+pathToEditor+imageSrcDetailed+'&language='+language+'" target="_blank">'+editImageText+'</a></div>';
									foundDetailedImage = true;
									break;
								}
							}
						}
					}
					
					if (!foundDetailedImage)
						td.innerHTML = "&nbsp;";

					trs[i].appendChild(td);
				}
				
				isTableHeader = false;			
			}
		}
		else
		{
			var e = mediamanagerForm.getElementsByTagName("div");
			
			for(var i=0;i<e.length;i++)
			{
				if (e[i].className == 'imgOutline' && e[i].innerHTML.indexOf('folderup_32.png') == -1 && e[i].innerHTML.indexOf('folder.png') == -1)
				{
					var images = e[i].getElementsByTagName("img");
					var imageSrc = "";
					
					if (images.length > 0)
					{
						imageSrc = phpimageeditor_urlencode(images[0].src.replace(hostPath,'../../../'));
		
						if (phpimageeditor_file_is_image(imageSrc))
							e[i].innerHTML += '<a style="background-position: 5px 0; background-image: url('+pathToPlugin+'images/edit.gif); background-repeat: no-repeat; padding-bottom: 4px; padding-left: 22px; display: block;" href="'+pathToEditor+imageSrc+'&language='+language+'" target="_blank">'+editImageText+'</a>';
					}
				}
			}
		}
	}
	else
	{
		var articledivs = document.getElementsByTagName("div");
		var foundManager = false;
		var foundItem = false;
		for(var i=0;i<articledivs.length;i++)
		{
			if (articledivs[i].className == 'manager')
				foundManager = true;
			else if (articledivs[i].className == 'item')
				foundItem = true;
				
			if (foundManager && foundItem)
				break;
		}		
			
		if (foundManager && foundItem)
		{
			for(var i=0;i<articledivs.length;i++)
			{
				if (articledivs[i].className == 'item')
				{
					var imagesArticle = articledivs[i].getElementsByTagName("img");
					var imageSrcArticle = "";
					
					if (imagesArticle.length > 0 && imagesArticle[0].src.indexOf('folder.gif') == -1 && phpimageeditor_file_is_image(imagesArticle[0].src))
					{
						imageSrcArticle = phpimageeditor_urlencode(imagesArticle[0].src.replace(hostPath,'../../../'));
						articledivs[i].innerHTML += '<a style="position: absolute; top: -38px;" href="'+pathToEditor+imageSrcArticle+'&language='+language+'" target="_blank">'+editImageText+'</a>';
					}
				}
			}
		}
	}
}

function phpimageeditor_urlencode(str) 
{
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // %          note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
                                     
    var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
    var ret = str.toString();
    
    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };
    
    // The histogram is identical to the one in urldecode.
    histogram['!']   = '%21';
    histogram['%20'] = '+';
    
    // Begin with encodeURIComponent, which most resembles PHP's encoding functions
    ret = encodeURIComponent(ret);
    
    for (search in histogram) {
        replace = histogram[search];
        ret = replacer(search, replace, ret) // Custom replace. No regexing
    }
    
    // Uppercase for full PHP compatibility
    return ret.replace('/(\%([a-z0-9]{2}))/g', function(full, m1, m2) {
        return "%"+m2.toUpperCase();
    });
    
    return ret;
}

function phpimageeditor_file_is_image(filePath)
{
	filePath = filePath.toLowerCase();
	return (filePath.indexOf('com_media') == -1 && (filePath.phpimageeditor_add_editlink_endswith("jpg") || filePath.phpimageeditor_add_editlink_endswith("gif") || filePath.phpimageeditor_add_editlink_endswith("png")));
}