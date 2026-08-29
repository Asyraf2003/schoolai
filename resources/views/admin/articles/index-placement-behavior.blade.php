<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  (() => {
    document.querySelectorAll('[data-placement-sortable]').forEach((list) => {
      const placement = list.dataset.placementSortable;
      const form = document.querySelector(`[data-placement-order-form="${placement}"]`);
      const inputHost = form?.querySelector('[data-placement-order-inputs]');
      const saveButton = form?.querySelector('[data-placement-save]');

      if (!form || !inputHost || !saveButton) return;

      let draggedItem = null;

      const placementItems = () => Array.from(list.querySelectorAll('[data-placement-item]'));

      const sync = () => {
        const items = placementItems();
        inputHost.replaceChildren();

        items.forEach((item, index) => {
          const position = item.querySelector('.article-placement-position');
          const up = item.querySelector('[data-placement-move="up"]');
          const down = item.querySelector('[data-placement-move="down"]');
          const input = document.createElement('input');

          if (position) position.textContent = String(index + 1);
          if (up) up.disabled = index === 0;
          if (down) down.disabled = index === items.length - 1;

          input.type = 'hidden';
          input.name = 'article_ids[]';
          input.value = item.dataset.articleId;
          inputHost.appendChild(input);
        });

        saveButton.disabled = false;
      };

      list.addEventListener('pointerdown', (event) => {
        const handle = event.target.closest('[data-placement-handle]');
        const item = handle?.closest('[data-placement-item]');
        if (item) item.dataset.dragArmed = 'true';
      });

      list.addEventListener('dragstart', (event) => {
        const item = event.target.closest('[data-placement-item]');
        if (!item || item.dataset.dragArmed !== 'true') {
          event.preventDefault();
          return;
        }

        draggedItem = item;
        item.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
      });

      list.addEventListener('dragover', (event) => {
        if (!draggedItem) return;
        event.preventDefault();

        const siblings = placementItems().filter((item) => item !== draggedItem);
        const next = siblings.find((item) => {
          const rect = item.getBoundingClientRect();
          return event.clientY < rect.top + (rect.height / 2);
        });

        list.insertBefore(draggedItem, next ?? null);
      });

      list.addEventListener('drop', (event) => {
        if (!draggedItem) return;
        event.preventDefault();
        sync();
      });

      list.addEventListener('dragend', () => {
        if (!draggedItem) return;
        draggedItem.classList.remove('is-dragging');
        delete draggedItem.dataset.dragArmed;
        draggedItem = null;
      });

      list.addEventListener('pointerup', () => {
        placementItems().forEach((item) => delete item.dataset.dragArmed);
      });

      list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-placement-move]');
        if (!button) return;

        const item = button.closest('[data-placement-item]');
        if (!item) return;

        if (button.dataset.placementMove === 'up') {
          const previous = item.previousElementSibling;
          if (previous?.matches('[data-placement-item]')) {
            list.insertBefore(item, previous);
            sync();
          }
          return;
        }

        const next = item.nextElementSibling;
        if (next?.matches('[data-placement-item]')) {
          list.insertBefore(next, item);
          sync();
        }
      });
    });
  })();
</script>
