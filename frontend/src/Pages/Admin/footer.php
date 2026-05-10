    <script>
        // кнопки отмена на страницах создания и редактирования
    	document.querySelector('.btn.cancel')?.addEventListener('click', (e)=>{
        if(!confirm("Are you sure you want to discard changes?")){
            e.preventDefault();
            return;
        }
    });
    </script>
	<?php if($_SESSION['name'] != ADMIN_NAME &&
    		(!isset($_GET['method']) || ((isset($_GET['method']) && (($_GET['method']) != 'edit' && ($_GET['method']) != 'create'))) )
    ){?>
        <div class="poster resize-responsive">
          <div class="poster-image"></div>
          <div class="poster-body pad-10">
            <p class="no-margin no-pad black-clr">
              Peye pwomosyon, mete kontni w dirèk sou paj akèy la pou plis moun ka wè, angaje, epi reyaji.
            </p>
            <p class="no-margin no-pad red-clr" style="margin-top: 5px;">PEZE BOUTON SA.</p>
          </div>
        </div>
        <script>
        	//
            try{
                posting();
            } catch(err){
                alert(err);
            }
            
            // align the poster correctly
            let adjacentElement = document.body.querySelector(".container.mt-4");
            const poster = document.querySelector(".poster");
            setTimeout(()=>{
                let rect = adjacentElement.getBoundingClientRect();
                document.querySelector(".poster").style.top = `${rect.bottom + 10}px`;
                centerPoster();
            }, 200);
            
            async function centerPoster(){
                /*
                * center a 'fixed' positioned poster
                * only for mobile displays
                */
                console.log('centering fixed poster');
                const poster = document.querySelector(".poster");
                if(window.innerWidth > 768 || !poster) return;
                let stls = window.getComputedStyle(poster);
                //rollback if its not fixed. css is the best alternative
                if(stls.position === 'relative' || stls.position === 'static') return;
                
                let rc = document.querySelector(".container.mt-4"); // relativeContainer
                let firstChild = rc.children[0];
                stls = rc ? window.getComputedStyle(rc) : null;
                let cstls = window.getComputedStyle(firstChild);
                let rw = rc ? rc.clientWidth : window.innerWidth;
                // const rec = poster.getBoundingClientRect();
                let fs = window.innerWidth - rw;// freeSpace
                let ppl = stls ? Number(/[0-9]+[.]?[0-9]+/.exec(stls.paddingLeft)) : 0;
                let ppr = stls ? Number(/[0-9]+[.]?[0-9]+/.exec(stls.paddingRight)) : 0;
                let cpl = stls ? Number(/[0-9]+[.]?[0-9]+/.exec(cstls.paddingLeft)) : 0;
                let cpr = stls ? Number(/[0-9]+[.]?[0-9]+/.exec(cstls.paddingRight)) : 0;
                poster.style.setProperty("width", `${rw - ppl - ppr - cpl - cpr}px`);
                fs = fs + ppl + ppr + cpl;
                poster.style.setProperty("margin-left", `${fs / 2}px`);                
            }
        </script>
    <?php }?>
<!-- end of body content -->
	</div>
    <script>
    let profileModal = document.getElementById('profileModal');

    function openProfileModal(formId = "profileForm") {
        profileModal.style.display = 'block';
        let form = profileModal.querySelector('#' + formId);
        if(form){
            form.style.display = 'block';
        }
    }

    function closeProfileModal() {
        profileModal.style.display = 'none';
        // Reset form
        const profileForm = document.getElementById('profileForm');
        const avatarForm = document.getElementById('avatar-edit-form');
        profileForm.reset();
        avatarForm.reset();
        profileForm.style.display = 'none';
        avatarForm.style.display = 'none';
    }

    function updateProfile(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Validate passwords if new password is provided
        if (data.new_password) {
            if (data.new_password !== data.confirm_password) {
                alert('New passwords do not match!');
                return;
            }
            if (data.new_password.length < 6) {
                alert('New password must be at least 6 characters long!');
                return;
            }
        }

        // Update the fetch URL to match your dbReader.php path
        fetch('/php/dbReader.php?q=updateProfile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            if (result.response === 'success') {
                alert('Profile updated successfully!');
                closeProfileModal();
                location.reload(); // Reload to show updated data
            } else {
                alert('Error: ' + (result.message || 'Failed to update profile'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update profile. Please try again.');
        });
    }

	async function saveProfileAvatar(event){
        event.preventDefault();
        const form = event.target;
        let formData = new FormData(form);
        const linkedImages = [];
        try{
            let response = await fetch('/admin/upload.php', {
                method: "POST",
                body: formData
            });

            let data = await response.json();
            if(data.success){
                linkedImages.push(data);
                response = await fetch('/php/dbReader.php?q=updateUserAvatar', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({url: data.url, name: "<?php echo $_SESSION['name']; ?>"})
                });

                data = await response.json();
                if(data.success){
                    // show alert
                } else {
                    alert("failed to update profile: " + data.message);
                    linkedImages.forEach((img) => {
                        formData = new FormData();
                        formData.append(img.url);
                        fetch(`/php/dbReader.php?q=deleteimage&id=${img.id}`, {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json)
                        .then(data => {
                            console.log(data.message);
                        })
                    });
                }
            }
        } catch(error){
			console.log(error.message);
            linkedImages.forEach((img) => {
                formData = new FormData();
                formData.append(img.url);
                fetch(`/php/dbReader.php?q=deleteimage&id=${img.id}`, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json)
                .then(data => {
                    console.log(data.message);
                })
            });
		}
        
        closeProfileModal();
        location.reload();
    }

const navBar = document.querySelector(".navbar")
const mainBody = document.querySelector(".container.mt-4");
var mainBodyStyles = window.getComputedStyle(mainBody);
var numberRegex = /[0-9]+[.]?[0-9]+/;
var currentDisplay = window.innerWidth;
let rect = mainBody.getBoundingClientRect();

mainBody.style.top = `${navBar.clientHeight + Number(numberRegex.exec(mainBodyStyles.marginTop))}px`;
document.body.style.marginBottom = `${Number(numberRegex.exec(mainBody.style.top)) + 50}px`;

window.addEventListener('resize', ()=>{
    if(window.innerWidth < 768 && currentDisplay > 768){
        centerPoster();
    }
    if((window.innerWidth > 768 && currentDisplay < 768) || 
        (window.innerWidth < 768 && currentDisplay > 768) ||
        (window.innerWidth > 992 && currentDisplay < 992) ||
        (window.innerWidth < 992 && currentDisplay > 992)
    ){
        mainBody.style.top = `${navBar.clientHeight + Number(numberRegex.exec(mainBodyStyles.marginTop))}px`;
        document.body.style.marginBottom = `${Number(numberRegex.exec(mainBody.style.top)) + 50}px`;
        currentDisplay = window.innerWidth;
        rect = mainBody.getBoundingClientRect();
        poster?.style.setProperty("top",`${rect.bottom + 10}px`);
    }
});
    function logoutUser() {
        fetch('/path/to/dbReader.php?q=userlogout')
            .then(response => response.json())
            .then(result => {
                if (result.response === 'success') {
                    window.location.href = '/login.php';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target === profileModal) {
            closeProfileModal();
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && profileModal.style.display === 'block') {
            closeProfileModal();
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Дополнительные скрипты для админ-панели -->
    <script>
        // Автозакрытие алертов через 5 секунд
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Подтверждение удаления
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }

        // Превью изображений перед загрузкой
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                    preview.classList.remove('d-none');
                });
                
                reader.readAsDataURL(file);
            }
        }

        // Инициализация превью для всех file input
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const previewId = this.getAttribute('data-preview');
                if (previewId) {
                    previewImage(this, previewId);
                }
            });
        });

        // Динамическое обновление счетчиков символов для textarea
        document.querySelectorAll('textarea[data-max-length]').forEach(textarea => {
            const maxLength = textarea.getAttribute('data-max-length');
            const counterId = textarea.getAttribute('data-counter');
            
            if (counterId) {
                const counter = document.getElementById(counterId);
                if (counter) {
                    textarea.addEventListener('input', function() {
                        const remaining = maxLength - this.value.length;
                        counter.textContent = remaining + ' символов осталось';
                        counter.className = remaining < 50 ? 'form-text text-danger' : 'form-text text-muted';
                    });
                }
            }
        });
        function handleCheckBox(){
            let check_label = document.querySelector('label.check');
            if(check_label){
                let check_inp = check_label.querySelector("input");
                if(check_inp){
                    const selectContainer = check_label.parentElement.querySelector('div');
                    const selectField = selectContainer.querySelector('select');
                    if(!selectContainer || !selectField) return;

                    check_inp.addEventListener("change", ()=>{
                        if(check_inp && check_inp.checked){
                            selectContainer.style.display = 'block';
                            selectField.disabled = false;
                        } else {
                            selectContainer.style.display = 'none';
                            selectField.disabled = true;
                        }
                    })
                } else {
                    console.log('')
                }
            } else {
                console.log('')
            }
        }

            handleCheckBox();
    </script>
</body>
</html>