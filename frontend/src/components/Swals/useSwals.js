import Swal from 'sweetalert2'

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
})

export function RegularSwal({ icon, title, message }) {
  Toast.fire({
    icon: icon,
    title: title,
    text: message,
  })
}

export function ConfirmationSwal({ icon, title, message, confirm_text, callBack }) {
  Swal.fire({
    title: title,
    text: message,
    icon: icon,
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: confirm_text,
  }).then((result) => {
    if (result.isConfirmed) {
      callBack()
    }
  })
}
