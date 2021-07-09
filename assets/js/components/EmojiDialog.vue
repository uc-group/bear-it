<template>
  <v-dialog v-model="dialogVisible" hide-overlay>
    <v-card class="emoji-dialog" :class="{'emoji-dialog--details': details}">
      <v-card-text>
        <div class="emoji-dialog__content">
          <div class="emoji-dialog__category">
            <ul class="emoji-dialog__category-list">
              <li><a href="#" @click="group = null; subgroup = null; query = ''">All</a></li>
              <li v-for="(g, groupId) in lib.groups">
                <a href="#" @click="group = groupId; subgroup = null">{{ g.name }}</a>
                <ul v-for="(s, subgroupId) in g.subgroups" v-show="group === groupId">
                  <li>
                    <a href="#" @click="subgroup = subgroupId">{{ s.name }}</a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
          <div class="emoji-dialog__emojis">
            <div class="emoji-dialog__search">
              <v-text-field ref="search" v-model="query" placeholder="enter phrase..." clearable></v-text-field>
              <v-switch class="emoji-dialog__details-switch" v-model="details" label="Show emoji names"></v-switch>
            </div>
            <div class="emoji-dialog__result">
              <div class="emoji" v-for="e in emojis" @click="$emit('selected', lib.data[e].char)">
                <span class="emoji__char">{{ lib.data[e].char }}</span>
                <span class="emoji__name" v-show="details">{{ lib.data[e].name }}</span>
              </div>
            </div>
          </div>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text color="primary" @click="dialogVisible = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import emojiLib from '~/lib/emoji.json'

export default {
  props: {
    visible: Boolean
  },
  data() {
    return {
      lib: JSON.parse(JSON.stringify(emojiLib)),
      group: null,
      subgroup: null,
      query: '',
      details: false
    }
  },
  computed: {
    dialogVisible: {
      get() {
        return this.visible
      },
      set(value) {
        this.$emit('update:visible', value)
      }
    },
    emojis() {
      if (this.query) {
        return this.findEmoji(this.query)
      }

      if (!this.group) {
        return Object.keys(emojiLib.data)
      }

      if (!this.subgroup) {
        console.log(Object.values(emojiLib.groups[this.group].subgroups).map((s) => s.codes))
        return Object.values(emojiLib.groups[this.group].subgroups).map((s) => s.codes)
            .reduce((acc, val) => {
              acc.push(...val)

              return acc
            }, [])
      }

      return emojiLib.groups[this.group].subgroups[this.subgroup].codes
    }
  },
  methods: {
    findEmoji(query) {
      const regex = new RegExp(query, 'i')

      return Object.values(emojiLib.data).filter((e) => regex.test(e.name)).map((e) => e.code)
    }
  },
  watch: {
    dialogVisible(to) {
      if (to) {
        this.query = ''
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.emoji-dialog {
  &__content {
    display: flex;
    padding: 20px;
    height: 60vh;
  }

  &__details-switch {
    margin-left: 50px;
  }

  &__category {
    width: 200px;
    flex: 0 0 auto;
    overflow: auto;
  }

  &__emojis {
    padding: 20px;
    flex-direction: column;
    display: flex;
    width: 100%;
  }

  &__search {
    flex: 0 0 auto;
    display: flex;
  }

  &__result {
    overflow: auto;
    display: flex;
    flex-wrap: wrap;
  }
}

.emoji {
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin: 5px;
  text-align: center;
  display: flex;
  flex-direction: column;
  flex: 0 1 auto;
  cursor: pointer;
  min-width: 35px;
  max-width: 80px;

  &:hover {
    background-color: lighten(#b2dbfb, 5%);
  }

  &__char {
    font-size: 24px;
  }

  &__name {
    font-size: 10px;
    line-height: 10px;
    margin-top: 5px;
  }
}
</style>
