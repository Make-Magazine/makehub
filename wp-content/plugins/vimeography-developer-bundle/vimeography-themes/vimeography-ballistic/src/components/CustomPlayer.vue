<script>
  import { Player } from 'vimeography-blueprint';

  const template = `
    <div class="vimeography-players-container">
      <h2 class="vimeography-title">{{activeVideo.name}}</h2>
      <player
        v-bind:activeVideo="activeVideo"
        v-bind:index="index"
        v-bind:key="activeVideo.id">
      </player>
    </div>
  `;

  const CustomPlayer = {
    props: ['activeVideo', 'index'],
    template,
    components: {
      Player
    }
  }

  export default CustomPlayer;
</script>

<style scoped>
  .vimeography-title {
    color: #444444;
    font-size: 12px;
    font-weight: bold;
    line-height: 23px;
    margin-bottom: 5px;
    text-transform: uppercase;
  }

  /deep/ .vimeography-player-container {
    margin-bottom: 2rem;
  }
</style>
