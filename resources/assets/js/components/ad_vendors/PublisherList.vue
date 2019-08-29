<template>
    <v-layout wrap>
      <v-flex xs12 sm12 md12>
          <v-autocomplete
            :items="publishers" :filter="customFilter"
            item-text="name" item-value="id"
            :allow-overflow="false" filled multiple
            v-model="selectedPublishers"
          ></v-autocomplete>
      </v-flex>

      <v-flex xs12 sm12 md12>
        <v-list>
          <v-subheader>Selected Publishers</v-subheader>
          <v-list-tile v-for="(item, idx) in publisherItems" :key="item.id">
            <v-list-tile-content>
              <v-list-tile-title v-text="item.name"></v-list-tile-title>
            </v-list-tile-content>

            <v-list-tile-action @click="onPublisherDelete(idx, item)">
              <v-btn icon>
                <v-icon color="red" dark>delete</v-icon>
              </v-btn>
            </v-list-tile-action>
          </v-list-tile>
        </v-list>
      </v-flex>
      
  </v-layout>

</template>

<script>
  export default {
    props: {
      publishers: Array
    },
    data () {
      return {
        selectedPublishers: []
      }
    },
    mounted() {
      console.log('Publisher list component mounted.');
    },
    created() {
        var self = this;
        Event.$on('dialog-closed', function() {
          self.setupSelectedPublishers([]);
        });
        Event.$on('edit-vendor', function(vendor) {
          self.setupSelectedPublishers(vendor.publishers);
        });
    },
    computed: {
      publisherItems: function() {
        var self = this;
        var itemList = [];

        this.selectedPublishers.forEach(function(publisherId, idx) {
          var publisherName = self.getPublisherName(publisherId);
          var publisher = {'id': publisherId, 'name': publisherName};
          itemList.push(publisher);
        });
        return itemList;
      }
    },
    watch: {
      selectedPublishers() {
        var itemList = [];
        this.selectedPublishers.forEach(function(publisherId, idx) {
          itemList.push({'id': publisherId});
        });
        Event.$emit('vendor-publisher-updated', itemList);
      }
    },
    methods: {
      setupSelectedPublishers(items) {
        var itemList = [];
        items.forEach(function(publisher, idx) {
          itemList.push(publisher.id);
        });
        this.selectedPublishers = itemList;
      },
      onPublisherDelete(idx, publisher) {
        this.$delete(this.selectedPublishers, idx);
      },
      getPublisherName(publisherId) {
        var publisher = this.getPublisher(publisherId);
        return publisher.name || '';
      },
      getPublisher(publisherId) {
        var idx = this.publishers.findIndex(x => x.id === publisherId);
        if (idx >= 0) {
          return this.publishers[idx];
        }
        return {};
      },
      customFilter(item, queryText, itemText) {
        var textOne = item.name.toLowerCase();
        var searchText = queryText.toLowerCase();
        return textOne.indexOf(searchText) > -1;
      },
    }
  }
</script>
