<?php

namespace Yoeunes\Rateable\Services;

class Raty
{
    protected $element;

    public function element($element)
    {
        $this->element = $element;

        return $this;
    }

    public function render(array $options = [])
    {
        return $this->raty($this->raty_options($options));
    }

    private function raty_options(array $options = [])
    {
        $options = array_merge(config('rateable.raty.options'), $options);

        return json_encode($options);
    }

    public function jsrender(array $options = [])
    {
        return '<script type="text/javascript">'.$this->render($options).'</script>';
    }

    public function raty($options = '', $params = null)
    {
        $params = null !== $params ? ', '.$params : '';

        return '$("'.$this->element.'").raty('.$options.$params.');';
    }

    public function score($score = null)
    {
        return $this->raty('score', $score);
    }

    public function click(int $number)
    {
        return $this->raty('click', $number);
    }

    public function readOnly(bool $status)
    {
        return $this->raty('readOnly', $status);
    }

    public function cancel(bool $status)
    {
        return $this->raty('cancel', $status);
    }

    public function reload()
    {
        return $this->raty('reload');
    }

    public function set(array $params = [])
    {
        return $this->raty(json_encode($params));
    }

    public function destroy()
    {
        return $this->raty('destroy');
    }

    public function move(int $number)
    {
        return $this->raty($number);
    }
}
