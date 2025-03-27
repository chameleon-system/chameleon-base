<?php

namespace ChameleonSystem\CoreBundle\Field;

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldThemePaths extends \TCMSFieldText
{
    public function GetHTML(): string
    {
        $id = uniqid('theme-path-editor-', false);
        $html = $this->getJavaScript($id);
        $html .= $this->renderSortableList($id);
        $html .= <<<HTML
<div id="{$id}-textarea" class="d-none">
    {$this->getTextareaHtml()}
</div>
HTML;

        return $html;
    }

    private function getTextareaHtml(): string
    {
        return parent::GetHTML();
    }

    private function renderSortableList(string $id): string
    {
        $paths = array_filter(array_map('trim', explode("\n", $this->data)));

        // Invert the list: last entry is first
        $paths = array_reverse($paths);

        $html = sprintf('<ul id="%s" class="list-group mb-3">', $id);
        foreach ($paths as $path) {
            $escapedPath = htmlspecialchars($path, ENT_QUOTES);
            $html .= <<<HTML
<li class="list-group-item d-flex justify-content-between align-items-center list-group-item-action" style="cursor: grab;" draggable="true">
    <div class="d-flex align-items-center">
        <i class="fas fa-code-branch me-2 text-muted"></i>
        <span class="path-text">$escapedPath</span>
    </div>
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-secondary copy-path" title="{$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_copy_path')}"><i class="fas fa-copy"></i></button>
        <button type="button" class="btn btn-outline-danger remove-path" title="{$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_remove_path')}"><i class="fas fa-trash-alt"></i></button>
    </div>
</li>
HTML;
        }

        $html .= '</ul>';

        $html .= <<<HTML
<div class="d-flex justify-content-between align-items-center mb-2" style="gap: 1rem;">
    <div class="input-group" style="max-width: 500px;">
        <input type="text" class="form-control" placeholder="{$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_add_path')}…" id="{$id}-input" />
        <button class="btn btn-outline-secondary" type="button" id="{$id}-add">
            <i class="fas fa-plus"></i>
        </button>
    </div>
    <a href="#" class="btn btn-link btn-sm toggle-textarea" data-target-id="{$id}-textarea">
        <i class="fas fa-chevron-down"></i> {$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_show_field')}
    </a>
</div>
<div id="{$id}-textarea" class="d-none">
    {$this->getTextareaHtml()}
</div>
HTML;

        return $html;
    }

    private function getJavaScript(string $listId): string
    {
        return <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('$listId');
    const textarea = list.nextElementSibling;
    const input = document.getElementById('$listId-input');
    const addButton = document.getElementById('$listId-add');

    let dragged;

    function bindDragEvents(item) {
        item.addEventListener('dragstart', function (e) {
            dragged = this;
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragover', function (e) {
            e.preventDefault();
        });

        item.addEventListener('drop', function (e) {
            e.preventDefault();
            if (dragged !== this) {
                const rect = this.getBoundingClientRect();
                const dropAbove = (e.clientY - rect.top) < (rect.height / 2);
                if (dropAbove) {
                    this.parentNode.insertBefore(dragged, this);
                } else {
                    this.parentNode.insertBefore(dragged, this.nextSibling);
                }
                updateTextarea();
            }
        });
    }

    function updateTextarea() {
        const items = list.querySelectorAll('.path-text');
        const values = Array.from(items).reverse().map(el => el.textContent.trim());
        textarea.value = values.join("\\n");
    }

    function addNewPath() {
        const value = input.value.trim();
        if (!value) return;

        const existing = Array.from(list.querySelectorAll('.path-text')).map(e => e.textContent.trim());
        if (existing.includes(value)) {
            input.value = '';
            return;
        }

        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center list-group-item-action';
        li.style.cursor = 'grab';
        li.setAttribute('draggable', 'true');

        li.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-code-branch me-2 text-muted"></i>
                <span class="path-text"></span>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary copy-path" title="{$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_copy_path')}">
                    <i class="fas fa-copy"></i>
                </button>
                <button type="button" class="btn btn-outline-danger remove-path" title="{$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_remove_path')}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

        li.querySelector('.path-text').textContent = value;
        bindDragEvents(li);
        list.insertBefore(li, list.firstChild); // insert at top (bottom of config)
        input.value = '';
        updateTextarea();
    }

    list.querySelectorAll('li').forEach(bindDragEvents);

    addButton.addEventListener('click', addNewPath);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            addNewPath();
            e.preventDefault();
        }
    });

        // Copy and remove buttons
    list.addEventListener('click', function (e) {
        if (e.target.closest('.remove-path')) {
            const item = e.target.closest('li');
            if (item) {
                item.remove();
                updateTextarea();
            }
        }

        if (e.target.closest('.copy-path')) {
            const item = e.target.closest('li');
            if (item) {
                const text = item.querySelector('.path-text')?.textContent;
                if (text) {
                    navigator.clipboard.writeText(text).then(() => {
                        e.target.closest('.copy-path').classList.add('text-success');
                        setTimeout(() => {
                            e.target.closest('.copy-path').classList.remove('text-success');
                        }, 800);
                    });
                }
            }
        }
    });

    // Toggle textarea visibility
    document.querySelectorAll('.toggle-textarea').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target-id');
            const target = document.getElementById(targetId);
            if (target) {
                const isHidden = target.classList.contains('d-none');
                target.classList.toggle('d-none');
                this.innerHTML = isHidden
                    ? '<i class="fas fa-chevron-up"></i> {$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_hide_field')}'
                    : '<i class="fas fa-chevron-down"></i> {$this->getTranslator()->trans('chameleon_system_core.field.snippet_chain_show_field')}';
            }
        });
    });
});
</script>
HTML;
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
