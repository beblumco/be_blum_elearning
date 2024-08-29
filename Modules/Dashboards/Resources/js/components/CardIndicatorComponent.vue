<template>
    <div class="col-lg-4 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="media align-items-center">

            <span class="mr-4" v-if="icon == 'dev_graphics'">
              <svg width="50" height="53" viewBox="0 0 50 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="7.11688" height="52.1905" rx="3.55844" transform="matrix(-1 0 0 1 49.8184 0)" fill="#012f54"/>
                <rect width="7.11688" height="37.9567" rx="3.55844" transform="matrix(-1 0 0 1 35.585 14.2338)" fill="#012f54"/>
                <rect width="7.11688" height="16.6061" rx="3.55844" transform="matrix(-1 0 0 1 21.3516 35.5844)" fill="#012f54"/>
                <rect width="8.0293" height="32.1172" rx="4.01465" transform="matrix(-1 0 0 1 8.0293 20.0732)" fill="#012f54"/>
              </svg>
            </span>

            <span class="mr-4" v-if="icon == 'dev_points'">
              <svg width="50" height="50" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13.3184 6.59834C12.5373 5.8173 11.271 5.8173 10.49 6.59834L0.556031 16.5323C0.281453 16.8068 0.281453 17.2521 0.556031 17.5266L3.24911 20.2197C3.44481 20.4154 3.73705 20.4781 3.99582 20.3799C5.0289 19.9878 6.20067 20.2386 6.98098 21.0189C7.76129 21.7992 8.01214 22.971 7.62003 24.0041C7.52178 24.2628 7.5845 24.5551 7.78019 24.7508L10.4733 27.4439C10.7479 27.7185 11.1931 27.7185 11.4677 27.4439L21.4016 17.51C22.1826 16.7289 22.1826 15.4626 21.4016 14.6816L13.3184 6.59834Z" fill="#012f54"></path>
                  <path d="M27.4439 10.4734L24.7508 7.78025C24.5551 7.58456 24.2628 7.52185 24.0041 7.62009C22.971 8.0122 21.7993 7.76132 21.019 6.98101C20.2386 6.2007 19.9878 5.02893 20.3799 3.99585C20.4781 3.73711 20.4154 3.44484 20.2197 3.24914L17.5266 0.556062C17.252 0.281484 16.8068 0.281484 16.5322 0.556062L14.3128 2.77554C13.5317 3.55659 13.5317 4.82292 14.3128 5.60396L22.396 13.6872C23.1771 14.4683 24.4434 14.4683 25.2244 13.6872L27.4439 11.4677C27.7185 11.1932 27.7185 10.7479 27.4439 10.4734Z" fill="#012f54"></path>
              </svg>
            </span>

            <span class="mr-4" v-if="icon == 'dev_certificados'">
              <i class="dev_icons_menu fa fa-graduation-cap mb-1" style="font-size: 50px;color: #012f54"></i>
            </span>

            <span class="mr-4" v-if="icon == 'dev_training_ent_orange'">
              <i class="dev_icons_menu fa fa-clock-o mb-1" style="font-size: 50px;color: #ff7f00"></i>
            </span>

            <span class="mr-4" v-if="icon == 'dev_certificados_orange'">
              <i class="dev_icons_menu fa fa-graduation-cap mb-1" style="font-size: 50px;color: #ff7f00"></i>
            </span>

            <span class="mr-4" v-if="icon == 'dev_points_orange'">
              <svg width="50" height="50" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13.3184 6.59834C12.5373 5.8173 11.271 5.8173 10.49 6.59834L0.556031 16.5323C0.281453 16.8068 0.281453 17.2521 0.556031 17.5266L3.24911 20.2197C3.44481 20.4154 3.73705 20.4781 3.99582 20.3799C5.0289 19.9878 6.20067 20.2386 6.98098 21.0189C7.76129 21.7992 8.01214 22.971 7.62003 24.0041C7.52178 24.2628 7.5845 24.5551 7.78019 24.7508L10.4733 27.4439C10.7479 27.7185 11.1931 27.7185 11.4677 27.4439L21.4016 17.51C22.1826 16.7289 22.1826 15.4626 21.4016 14.6816L13.3184 6.59834Z" fill="#ff7f00"></path>
                  <path d="M27.4439 10.4734L24.7508 7.78025C24.5551 7.58456 24.2628 7.52185 24.0041 7.62009C22.971 8.0122 21.7993 7.76132 21.019 6.98101C20.2386 6.2007 19.9878 5.02893 20.3799 3.99585C20.4781 3.73711 20.4154 3.44484 20.2197 3.24914L17.5266 0.556062C17.252 0.281484 16.8068 0.281484 16.5322 0.556062L14.3128 2.77554C13.5317 3.55659 13.5317 4.82292 14.3128 5.60396L22.396 13.6872C23.1771 14.4683 24.4434 14.4683 25.2244 13.6872L27.4439 11.4677C27.7185 11.1932 27.7185 10.7479 27.4439 10.4734Z" fill="#ff7f00"></path>
              </svg>
            </span>

            <div class="media-body ml-1">
              <p class="mb-2">{{ title }}</p>
              <h3 class="mb-0 text-black font-w600">{{ quantity }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

</template>

<script>

export default {
    props: {
      title: String,
      quantity: String,
      icon: String
    },
    async mounted()
    {

    },
    data() {
        return {
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            api: document.querySelector('meta[name="csrf-token"]').getAttribute("api"),
        };
    },
    methods: {

    },
}
</script>

<style scoped>

</style>
