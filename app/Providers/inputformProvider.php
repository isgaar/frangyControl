<?php


namespace App\Providers;

use Collective\Html\FormBuilder as Form;
use Collective\Html\HtmlFacade as HTML;
use Illuminate\Support\ServiceProvider;

class inputformProvider extends ServiceProvider
{

    public function boot()
    {

        Form::macro('UTHidden', function ($name,$value)
        {
            $comp = '<div class="form-group">';
            if($value!=null){$comp.= Form::hidden($name, $value , array('id' =>$name));}else{$comp.= Form::hidden($name, old($name), array('id' =>$name));}
            $comp.= '</div>';
            return $comp;
        });


        Form::macro('UTImage', function ($name,$width=null,$heigth=null)
        {
            $comp = '<div class="form-group">';
            if($name!=null){$comp.=HTML::image($name, $name, ['style'=>"width: $width!important; heigth:$heigth!important"]);}else{$comp.= HTML::image($name, old($name));}
            $comp.= '</div>';
            return $comp;
        });


        Form::macro('UTFile', function ($name,$label,$title,$required=null)
        {
            $comp = '<div class="form-group">';
            if($required){$atributes['required']='';}
            $comp.= '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><div class="form-line" style="border: none!important;">';
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=> 'form-label'));}
            $comp.= '</div></div><div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">';
            $comp.=Form::file($name, array('id' => $name, 'class' => "form-control", 'title'=> $title , 'value'=> "{{old($name)}}") );
            $comp.= '</div></div>';
            return $comp;
        });


        Form::macro('UTNumeric', function ($name,$label,$title,$value=null,$errors,$length,$required=null,$readonly=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required){$atributes['required']='';}
            $atributes['id'] = $name;
            $atributes['class']='form-control';
            $atributes['onkeypress']='return UTNumeric(event);';
            $atributes['onkeyup']='UTZero(event,this)';
            $atributes['title']=$title;
            $atributes['maxlength']=$length;
            $atributes['style']='background-color: #ffffff color:#000000; text-align:right;';
            if($readonly!=null){$atributes['readonly']=$readonly;}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::text($name, $value, $atributes, array('id' => $name));}else{$comp.= Form::text($name, old($name), $atributes, array('id' => $name));}
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=>"form-label"));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });


        Form::macro('UTFloat', function ($name,$label,$title,$value=null,$errors,$length,$required=null,$readonly=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required){$atributes['required']='';}
            $atributes['id'] = $name;
            $atributes['class']='form-control';
            $atributes['onkeypress']='return UTFloat(event,this);';
            $atributes['onkeyup']='UTZero(event,this)';
            $atributes['title']=$title;
            $atributes['maxlength']=$length;
            $atributes['style']='background-color: #ffffff color:#000000; text-align:right;';
            if($readonly!=null){$atributes['readonly']=$readonly;}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::text($name, $value, $atributes, array('id' => $name));}else{$comp.= Form::text($name, old($name), $atributes, array('id' => $name));}
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=>'form-label'));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });


        Form::macro('UTText', function ($name,$label,$title,$value=null, $errors,$length,$required=null,$readonly=null,$click=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required != null){$atributes['required']='';}
            $atributes['id'] = $name;
            $atributes['class']='form-control';
            $atributes['onkeypress']='return UTText(event,this);';
            $atributes['title']=$title;
            $atributes['maxlength']=$length;
            $atributes['style']='background-color: #ffffff color:#000000;';
            //$atributes['style']='color: #000000;';
            if($click!=null){$atributes['onclick']=$click;}
            if($readonly!=null){$atributes['readonly']=$readonly;}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::text($name, $value, $atributes, array('id' =>$name));}else{$comp.= Form::text($name, old($name), $atributes, array('id' =>$name));}
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=>'form-label'));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });



        Form::macro('UTTextOnly', function ($name,$label,$title,$value=null, $errors,$length,$required=null,$readonly=null,$click=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required != null){$atributes['required']='';}
            
            $atributes['id'] = $name;
            $atributes['class']='form-control';
            $atributes['onkeypress']='return Letras(event);';
            $atributes['title']=$title;
            $atributes['maxlength']=$length;
            $atributes['style']='background-color: #ffffff color:#000000;';
            //$atributes['style']='color: #000000;';
            if($click!=null){$atributes['onclick']=$click;}
            if($readonly!=null){$atributes['readonly']=$readonly;}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::text($name, $value, $atributes, array('id' =>$name));}else{$comp.= Form::text($name, old($name), $atributes, array('id' =>$name));}
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=>'form-label'));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });



        Form::macro('UTTextMay', function ($name,$label,$title,$value=null, $errors,$length,$required=null,$readonly=null,$click=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required != null){$atributes['required']='';}
            $atributes['id'] = $name;
            $atributes['class']='form-control';
            $atributes['onkeypress']='return UTText(event,this);';
            $atributes['onkeyup']='mayus(this);';
            $atributes['title']=$title;
            $atributes['maxlength']=$length;
            $atributes['style']='background-color: #ffffff;';
            if($click!=null){$atributes['onclick']=$click;}
            if($readonly!=null){$atributes['readonly']=$readonly;}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::text($name, $value, $atributes, array('id' =>$name));}else{$comp.= Form::text($name, old($name), $atributes, array('id' =>$name));}
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=>'form-label'));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });


        Form::macro('UTEmail', function ($name,$label,$title,$value=null,$errors,$length,$required=null,$readonly=null,$click=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required != null){$atributes['required']='';}
            $atributes['id'] = $name;
            $atributes['class']='form-control';
            $atributes['onkeypress']='return UTText(event,this);';
            $atributes['title']=$title;
            $atributes['maxlength']=$length;
            $atributes['style']='background-color: #ffffff color:#000000;';
            if($click!=null){$atributes['onclick']=$click;}
            if($readonly!=null){$atributes['readonly']=$readonly;}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::email($name, $value, $atributes);}else{$comp.= Form::email($name, old($name), $atributes);}
            if($required){$comp.= Form::toHtmlString("<label class='form-label'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('class'=>'form-label'));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });



        Form::macro('UTList', function ($name,$label,$title,$options,$default = -1,$errors,$required=null,$onchange=null,$click=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required){$atributes['required']='';}
            if($onchange!=null){$atributes['onchange']=$onchange;}else{$atributes['onchange']='';}
            $atributes['id']= $name;
            $atributes['class']='form-control';
            if($click!=null){$atributes['onclick']=$click;}
            $comp.= '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><div class="form-line" style="border: none!important;">';
            if($required){$comp.= Form::toHtmlString("<label class='form-label' title='$title'>".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label ,array('class'=> 'form-label', 'title'=> $title));}
            $comp.= '</div></div><div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">';
            $comp.= Form::select($name, $options, $default, $atributes);
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });

        Form::macro('UTListclean', function ($name,$options,$default = -1,$errors,$required=null,$onchange=null,$click=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required){$atributes['required']='';}
            if($onchange!=null){$atributes['onchange']=$onchange;}else{$atributes['onchange']='';}
            $atributes['id']= $name;
            $atributes['class']='form-control';
            if($click!=null){$atributes['onclick']=$click;}
            $comp.= '</div></div>';
            $comp.= Form::select($name, $options, $default, $atributes);
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });


        Form::macro('UTCalendar', function ($name, $label,$title,$value=null, $errors, $required=null)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            if($required){$atributes['required']='';}
            $atributes['id']=$name;
            $atributes['title']=$title;
            $atributes['class']='datepicker form-control';
            $atributes['readonly']=' ';
            $atributes['placeholder']='Ingrese la fecha';
            if($required){$comp.= Form::toHtmlString("<label class=\"form-label\" style=\"font-weight: normal;color: #aaa;\">".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");}else{$comp .= Form::label($name, $label, array('style'=>"font-weight: normal;color: #aaa;"));}
            $comp.= '<div class="form-line">';
            if($value!=null){$comp.= Form::text($name, $value, $atributes, array('id' =>$name));}else{$comp.= Form::text($name, old($name), $atributes, array('id' =>$name));}
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });

        Form::macro('UTPassword', function ($name, $label, $title, $errors, $min, $length)
        {
            if($errors->has($name)) {$comp = '<div class="form-group has-error">';} else {$comp = '<div class="form-group form-float">';}
            $atributes['id'] = $name;
            $atributes['class'] = 'form-control';
            $atributes['title'] = $title;
            $atributes['required'] = true;
            $atributes['maxlength'] = $length;
            $atributes['minlength'] = $min;
            $atributes['style'] = 'background-color: #ffffff color:#000000;';
            $comp.= '<div class = "form-line" >';
            $comp.= Form::password($name, array('id' => $name));
            $comp.= Form::toHtmlString("<label class=\"form-label\" style=\"font-weight: normal;color: #aaa;\">".ucfirst($label)."<span class=\"required\" style='color:red!important;'>&nbsp;<strong>*</strong></span></label>");
            $comp.= '</div>';
            if($errors->has($name)) {$comp .= '<span class="help-block">'.$errors->first($name).'</span>';}
            $comp .= '</div>';
            return $comp;
        });
    }

}