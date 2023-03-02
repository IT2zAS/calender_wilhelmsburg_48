const showEditName = document.querySelector('#changeNameShow');
const editForm = document.querySelector('#editNameForm');
const shoEditImageForm = document.querySelector('#showEditImgForm');
const imgUploadBtn = document.querySelector('#imgSend');
const myForm = document.querySelector('#myForm');
const userImg = document.querySelector('#userImage');

//==============================================//
showEditName.addEventListener('click', (e) => {
  e.preventDefault();
  editForm.classList.toggle('hidden');
})

//================= edit user foto ==========================//
const formPost = (url,body) => {
  return new Promise((resolve,reject)=>{
    fetch(url,{
      method: "POST",
      body:body
    }).then(response=>{
      if(response.status ===200)
      {
        // response.text().then(text =>{
        //   console.log(text)
        // })
        // console.log(response)
        response.json().then(data => {
          resolve(data)

        }).catch(error => {
          console.log('1',error)
          reject(error)
        })
      }else{
        console.log('2',error)
        reject(new Error('can not send the data, response number is: ' + response.status))
      }
    }).catch(error => {

      reject(error)
    })


  })

}

const reloadPage =()=>{ window.location.reload()}
//============================================================//

myForm.addEventListener('submit', (e) =>{
  e.preventDefault();
  const userName  = document.querySelector('#user').innerHTML

  const imgInput = document.querySelector('#imgInput');
  if(imgInput.files.length>0)
  {

    let fileSuffix = imgInput.files[0].name.slice(imgInput.files[0].name.lastIndexOf('.')+1).toUpperCase();
    if(fileSuffix == 'GIF' || fileSuffix == 'PNG' || fileSuffix == 'JPG' || fileSuffix == 'TIFF' || fileSuffix == 'AI')
    {
      let image =imgInput.files[0];
       let imageName = image.name ;
       imageName = userName+'.'+fileSuffix


      const fd = new FormData();
      fd.append('userImg',imgInput.files[0]);
      let url = './UserPage.php';
      formPost(url,fd).then(data=>{
        console.log(data[0])
        reloadPage()

      })

    }
    else
    {
      let errorMessage = "Dieser File ist Not Foto";
      console.log(errorMessage)
    }
  }
  else
  {
    let errorMessage = "Bitte wählen Sie Eine Datei aus ";
    console.log(errorMessage)
  }


})