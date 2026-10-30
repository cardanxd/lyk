<?php

use App\Models\Faq;

it('active() excluye inactivos y ordena por sort_order', function () {
    Faq::factory()->create(['question' => 'B', 'sort_order' => 2]);
    Faq::factory()->create(['question' => 'A', 'sort_order' => 1]);
    Faq::factory()->create(['question' => 'X', 'sort_order' => 0, 'is_active' => false]);
    expect(Faq::active()->pluck('question')->all())->toBe(['A', 'B']);
});
