<template>
  <div>
      <div class="content">
          <router-view></router-view>
      </div>

  </div>
</template>

<script>
export default {

}
</script>

<style>

    body{
            font-family: Roboto;
            background-attachment: fixed;
            /* background: linear-gradient(0deg, rgba(22,152,112,1) 0%, rgba(56,193,114,1) 100%); */
            background-color: #eeeeee;
            background-repeat: no-repeat;
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;

    }

    *::-webkit-scrollbar {
    width: 1em;
    }

    *::-webkit-scrollbar-track {
    }

    *::-webkit-scrollbar-thumb {
    background-color: #f5b247;
    outline: 0px solid slategrey;
    border-radius: 2rem;
    }

</style>
