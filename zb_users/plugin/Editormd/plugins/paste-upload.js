/**
 * 粘贴上传文件
 * @author zsx (https://www.zsxsoft.com/)
 */
$(function () {
  const fn = () => {
    if (ContentEditor) {
      const classPrefix = ContentEditor.classPrefix
      const settings = ContentEditor.settings
      const itemName = classPrefix + 'image-file'

      ContentEditor.cm.getWrapperElement().addEventListener('paste', e => {
        if (e.clipboardData && e.clipboardData.items[0].type.indexOf("image") > -1) {
          const file = e.clipboardData.items[0].getAsFile()
          //const reader = new FileReader()
          //reader.onload = e => {
          //  const file = e.currentTarget.result
            const form = new FormData()
            const guid = (new Date).getTime()
            const action = settings.imageUploadURL + (settings.imageUploadURL.indexOf("?") >= 0 ? "&" : "?") + "guid=" + guid;
            form.append(itemName, file)
            console.log(action)
            console.log(form)
            $.ajax({
              type: 'post',
              contentType: false,
              processData: false,
              url: action, 
              data: form, 
              success: s => {
                const d = JSON.parse(s)
                ContentEditor.cm.replaceSelection("![](" + d.url + ")");
              }
            })
          //}
          //reader.readAsBlob(file)
        }
      })
      clearInterval(interval)
    }
  }
  const interval = setInterval(fn, 1000)
});
